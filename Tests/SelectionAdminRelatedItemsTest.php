<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Selection\Tests;

use Selection\Model\Selection;
use Selection\Model\SelectionContent;
use Selection\Model\SelectionProduct;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Model\Admin;
use Thelia\Model\Category;
use Thelia\Model\Currency;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Model\Product;
use Thelia\Model\TaxRule;
use Thelia\Test\IntegrationTestCase;
use Twig\Environment;

/**
 * The product and content lists of the selection edit screen, and its product search.
 *
 * Every request runs in an administration session that never visited the shop: the session
 * holds no front language, which is the case of an administrator who opens the back office
 * first.
 *
 * Runs on the test database of the project (`php bin/test-prepare`), never on the shop's.
 */
final class SelectionAdminRelatedItemsTest extends IntegrationTestCase
{
    private Category $category;

    private TaxRule $taxRule;

    private Currency $currency;

    private string $marker;

    protected function setUp(): void
    {
        $databaseName = $_SERVER['DATABASE_NAME'] ?? getenv('DATABASE_NAME');
        if (!\is_string($databaseName) || !str_ends_with($databaseName, '_test')) {
            self::fail(\sprintf('Refusing to run on the database "%s": use a *_test database.', (string) $databaseName));
        }

        parent::setUp();

        // Render the screens the way production does: the test environment turns on Twig's
        // strict_variables, which the back-office layout does not withstand (WebIntegrationTestCase
        // of the core does the same).
        $twig = static::getContainer()->get('twig');
        self::assertInstanceOf(Environment::class, $twig);
        $twig->disableStrictVariables();

        $fixtures = $this->createFixtureFactory();
        $this->category = $fixtures->category();
        $this->taxRule = $fixtures->taxRule();
        $this->currency = $fixtures->currency();
        // Titles and references carry a marker of the test, so that a search never meets the
        // products the test database already holds.
        $this->marker = 'zq'.bin2hex(random_bytes(4));
    }

    public function testTheEditScreenOffersTheProductSearchInTheAdministrationLanguage(): void
    {
        $selection = $this->selection([]);

        $response = $this->get('/admin/selection/update/'.$selection->getId(), 'fr_FR');

        self::assertSame(200, $response->getStatusCode(), (string) $response->getContent());
        $content = (string) $response->getContent();
        self::assertStringContainsString('id="product_search"', $content);
        self::assertStringContainsString('Titre ou référence du produit...', $content);
        self::assertStringContainsString('Sélectionner une catégorie pour afficher ces produits', $content);
    }

    public function testTheProductsOfTheSelectionAreListed(): void
    {
        $first = $this->product('Helmet '.$this->marker.' black');
        $second = $this->product('Helmet '.$this->marker.' white');
        $selection = $this->selection([$second, $first]);

        $response = $this->get('/admin/selection/update/showProduct/'.$selection->getId());

        self::assertSame(200, $response->getStatusCode(), (string) $response->getContent());
        $content = (string) $response->getContent();
        self::assertStringContainsString('Helmet '.$this->marker.' white', $content);
        self::assertStringContainsString('Helmet '.$this->marker.' black', $content);
        self::assertLessThan(
            strpos($content, 'Helmet '.$this->marker.' black'),
            strpos($content, 'Helmet '.$this->marker.' white'),
            'The products follow their position in the selection.',
        );
    }

    public function testTheVisibleProductsOfACategoryAreOffered(): void
    {
        $visible = $this->product('Helmet '.$this->marker.' visible');
        $this->product('Helmet '.$this->marker.' hidden', ['visible' => 0]);

        $response = $this->get('/admin/selection/update/getProductRelated/'.$this->category->getId());

        self::assertSame(200, $response->getStatusCode(), (string) $response->getContent());
        self::assertSame(
            [['id' => $visible->getId(), 'title' => 'Helmet '.$this->marker.' visible']],
            $this->json($response),
        );
    }

    public function testTheContentsOfTheSelectionAreListed(): void
    {
        $fixtures = $this->createFixtureFactory();
        $content = $fixtures->content($fixtures->folder(), ['title' => 'Guide '.$this->marker]);
        $selection = $this->selection([]);
        (new SelectionContent())
            ->setSelectionId($selection->getId())
            ->setContentId($content->getId())
            ->setPosition(1)
            ->save();

        $response = $this->get('/admin/selection/update/showContent/'.$selection->getId());

        self::assertSame(200, $response->getStatusCode(), (string) $response->getContent());
        self::assertStringContainsString('Guide '.$this->marker, (string) $response->getContent());
    }

    public function testTheContentsOfAFolderAreOffered(): void
    {
        $fixtures = $this->createFixtureFactory();
        $folder = $fixtures->folder();
        $content = $fixtures->content($folder, ['title' => 'Guide '.$this->marker]);

        $response = $this->get('/admin/selection/update/getContentRelated/'.$folder->getId());

        self::assertSame(200, $response->getStatusCode(), (string) $response->getContent());
        self::assertSame([['id' => $content->getId(), 'title' => 'Guide '.$this->marker]], $this->json($response));
    }

    public function testTheSearchFindsAProductByItsTitleInTheEditionLanguage(): void
    {
        $product = $this->product('Bell Moto 3 '.$this->marker.' Classic Black', ['ref' => 'REF-'.$this->marker.'-1']);
        $this->translate($product, 'fr_FR', 'Casque '.$this->marker.' noir');

        $results = $this->search('moto 3 '.$this->marker);

        self::assertSame(
            [['id' => $product->getId(), 'ref' => 'REF-'.$this->marker.'-1', 'title' => 'Bell Moto 3 '.$this->marker.' Classic Black']],
            $results,
        );
        self::assertSame([], $this->search('Casque '.$this->marker), 'Only the edition language is searched.');
    }

    public function testTheSearchFindsAProductByItsReference(): void
    {
        $product = $this->product('Helmet', ['ref' => 'REF-'.$this->marker.'-2']);

        self::assertSame([$product->getId()], array_column($this->search('ref-'.$this->marker), 'id'));
    }

    public function testTheSearchLeavesOutTheProductsAlreadyInTheSelection(): void
    {
        $inSelection = $this->product('Helmet '.$this->marker.' in');
        $outOfSelection = $this->product('Helmet '.$this->marker.' out');
        $selection = $this->selection([$inSelection]);

        self::assertSame(
            [$outOfSelection->getId()],
            array_column($this->search('Helmet '.$this->marker, $selection), 'id'),
        );
    }

    public function testTheSearchProposesFifteenProductsAtMostSortedByTitle(): void
    {
        for ($number = 20; $number > 0; --$number) {
            $this->product(\sprintf('Helmet %s %02d', $this->marker, $number));
        }

        $titles = array_column($this->search('Helmet '.$this->marker), 'title');

        self::assertCount(15, $titles);
        self::assertSame(\sprintf('Helmet %s 01', $this->marker), $titles[0]);
        self::assertSame(\sprintf('Helmet %s 15', $this->marker), $titles[14]);
    }

    public function testTheSearchReadsWildcardsAsPlainCharacters(): void
    {
        $this->product('Helmet '.$this->marker.' black');
        $discounted = $this->product('Helmet '.$this->marker.' 50% off');

        self::assertSame([$discounted->getId()], array_column($this->search($this->marker.' 50%'), 'id'));
        self::assertSame([], $this->search($this->marker.' _lack'));
    }

    public function testAProductWithoutTitleInTheEditionLanguageComesLast(): void
    {
        $untitled = $this->createFixtureFactory()->product(
            $this->category,
            $this->taxRule,
            $this->currency,
            ['ref' => 'A-'.$this->marker],
        );
        $titled = $this->product('Helmet', ['ref' => 'B-'.$this->marker]);

        self::assertSame([$titled->getId(), $untitled->getId()], array_column($this->search($this->marker), 'id'));
    }

    public function testTheSearchSentAsAFormIsAnswered(): void
    {
        $product = $this->product('Helmet '.$this->marker);

        $response = $this->get('/admin/selection/search-product', 'en_US', ['query' => 'Helmet '.$this->marker]);

        self::assertSame([$product->getId()], array_column($this->json($response), 'id'));
    }

    public function testAnAdministratorWithoutAccessToTheModuleReadsNothing(): void
    {
        $product = $this->product('Helmet '.$this->marker);
        $selection = $this->selection([$product]);
        $restricted = $this->createFixtureFactory()->restrictedAdmin([]);

        foreach ([
            '/admin/selection/search-product?query='.urlencode('Helmet '.$this->marker),
            '/admin/selection/update/showProduct/'.$selection->getId(),
            '/admin/selection/update/getProductRelated/'.$this->category->getId(),
        ] as $uri) {
            $response = $this->get($uri, 'en_US', [], $restricted);

            self::assertNotSame(200, $response->getStatusCode(), $uri);
            self::assertStringNotContainsString('Helmet '.$this->marker, (string) $response->getContent(), $uri);
        }
    }

    public function testAnEmptySearchProposesNothing(): void
    {
        $this->product('Helmet '.$this->marker);

        self::assertSame([], $this->search('   '));
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function product(string $title, array $overrides = []): Product
    {
        return $this->createFixtureFactory()->product(
            $this->category,
            $this->taxRule,
            $this->currency,
            ['title' => $title, 'locale' => 'en_US'] + $overrides,
        );
    }

    private function translate(Product $product, string $locale, string $title): void
    {
        $product->setLocale($locale)->setTitle($title)->save();
    }

    /**
     * @param list<Product> $products in the order of their position
     */
    private function selection(array $products): Selection
    {
        $selection = (new Selection())->setVisible(1)->setCode('selection-'.$this->marker);
        $selection->setLocale('en_US')->setTitle('Selection '.$this->marker);
        $selection->save();

        foreach (array_values($products) as $index => $product) {
            (new SelectionProduct())
                ->setSelectionId($selection->getId())
                ->setProductId($product->getId())
                ->setPosition($index + 1)
                ->save();
        }

        return $selection;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function search(string $query, ?Selection $selection = null): array
    {
        $parameters = ['query' => $query];
        if (null !== $selection) {
            $parameters['selection'] = (string) $selection->getId();
        }

        $response = $this->get('/admin/selection/search-product?'.http_build_query($parameters));
        self::assertSame(200, $response->getStatusCode(), (string) $response->getContent());

        return $this->json($response);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function json(Response $response): array
    {
        $decoded = json_decode((string) $response->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        return $decoded;
    }

    /**
     * An administration session in the given language, also the edition language, and no front
     * language.
     */
    /**
     * @param array<string, string> $form sent as a POST when given
     */
    private function get(string $uri, string $locale = 'en_US', array $form = [], ?Admin $admin = null): Response
    {
        $lang = $this->lang($locale);
        $session = new Session(new MockArraySessionStorage());
        $session->setAdminUser($admin ?? $this->createFixtureFactory()->admin(['locale' => $locale]));
        $session->set('thelia.current.admin_lang', $lang);
        $session->setAdminEditionLang($lang);
        self::assertNull($session->get('thelia.current.lang'));

        $request = [] === $form ? Request::create($uri) : Request::create($uri, 'POST', $form);
        $request->setSession($session);

        return $this->handleAsMainRequest($request);
    }

    /**
     * IntegrationTestCase pushes a synthetic request: it is taken off the stack while the
     * kernel handles this one, so that this request is the main request the controller reads.
     */
    private function handleAsMainRequest(Request $request): Response
    {
        $requestStack = static::getContainer()->get('request_stack');
        self::assertInstanceOf(RequestStack::class, $requestStack);
        $pushedRequests = [];
        while (null !== $pushedRequest = $requestStack->pop()) {
            $pushedRequests[] = $pushedRequest;
        }

        try {
            return self::$kernel->handle($request);
        } finally {
            while (null !== $requestStack->pop()) {
            }
            foreach (array_reverse($pushedRequests) as $pushedRequest) {
                $requestStack->push($pushedRequest);
            }
        }
    }

    private function lang(string $locale): Lang
    {
        $lang = LangQuery::create()->findOneByLocale($locale);
        self::assertInstanceOf(Lang::class, $lang, \sprintf('The test database has no %s language.', $locale));

        return $lang;
    }
}
