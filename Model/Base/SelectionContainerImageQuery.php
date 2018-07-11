<?php

namespace Selection\Model\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Selection\Model\SelectionContainerImage as ChildSelectionContainerImage;
use Selection\Model\SelectionContainerImageI18nQuery as ChildSelectionContainerImageI18nQuery;
use Selection\Model\SelectionContainerImageQuery as ChildSelectionContainerImageQuery;
use Selection\Model\Map\SelectionContainerImageTableMap;

/**
 * Base class that represents a query for the 'selection_container_image' table.
 *
 *
 *
 * @method     ChildSelectionContainerImageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSelectionContainerImageQuery orderBySelectionContainerId($order = Criteria::ASC) Order by the selection_container_id column
 * @method     ChildSelectionContainerImageQuery orderByFile($order = Criteria::ASC) Order by the file column
 * @method     ChildSelectionContainerImageQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildSelectionContainerImageQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildSelectionContainerImageQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildSelectionContainerImageQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildSelectionContainerImageQuery groupById() Group by the id column
 * @method     ChildSelectionContainerImageQuery groupBySelectionContainerId() Group by the selection_container_id column
 * @method     ChildSelectionContainerImageQuery groupByFile() Group by the file column
 * @method     ChildSelectionContainerImageQuery groupByVisible() Group by the visible column
 * @method     ChildSelectionContainerImageQuery groupByPosition() Group by the position column
 * @method     ChildSelectionContainerImageQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildSelectionContainerImageQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildSelectionContainerImageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSelectionContainerImageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSelectionContainerImageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSelectionContainerImageQuery leftJoinSelectionContainer($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionContainer relation
 * @method     ChildSelectionContainerImageQuery rightJoinSelectionContainer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionContainer relation
 * @method     ChildSelectionContainerImageQuery innerJoinSelectionContainer($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionContainer relation
 *
 * @method     ChildSelectionContainerImageQuery leftJoinSelectionContainerImageI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionContainerImageI18n relation
 * @method     ChildSelectionContainerImageQuery rightJoinSelectionContainerImageI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionContainerImageI18n relation
 * @method     ChildSelectionContainerImageQuery innerJoinSelectionContainerImageI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionContainerImageI18n relation
 *
 * @method     ChildSelectionContainerImage findOne(ConnectionInterface $con = null) Return the first ChildSelectionContainerImage matching the query
 * @method     ChildSelectionContainerImage findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSelectionContainerImage matching the query, or a new ChildSelectionContainerImage object populated from the query conditions when no match is found
 *
 * @method     ChildSelectionContainerImage findOneById(int $id) Return the first ChildSelectionContainerImage filtered by the id column
 * @method     ChildSelectionContainerImage findOneBySelectionContainerId(int $selection_container_id) Return the first ChildSelectionContainerImage filtered by the selection_container_id column
 * @method     ChildSelectionContainerImage findOneByFile(string $file) Return the first ChildSelectionContainerImage filtered by the file column
 * @method     ChildSelectionContainerImage findOneByVisible(int $visible) Return the first ChildSelectionContainerImage filtered by the visible column
 * @method     ChildSelectionContainerImage findOneByPosition(int $position) Return the first ChildSelectionContainerImage filtered by the position column
 * @method     ChildSelectionContainerImage findOneByCreatedAt(string $created_at) Return the first ChildSelectionContainerImage filtered by the created_at column
 * @method     ChildSelectionContainerImage findOneByUpdatedAt(string $updated_at) Return the first ChildSelectionContainerImage filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildSelectionContainerImage objects filtered by the id column
 * @method     array findBySelectionContainerId(int $selection_container_id) Return ChildSelectionContainerImage objects filtered by the selection_container_id column
 * @method     array findByFile(string $file) Return ChildSelectionContainerImage objects filtered by the file column
 * @method     array findByVisible(int $visible) Return ChildSelectionContainerImage objects filtered by the visible column
 * @method     array findByPosition(int $position) Return ChildSelectionContainerImage objects filtered by the position column
 * @method     array findByCreatedAt(string $created_at) Return ChildSelectionContainerImage objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildSelectionContainerImage objects filtered by the updated_at column
 *
 */
abstract class SelectionContainerImageQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Selection\Model\Base\SelectionContainerImageQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Selection\\Model\\SelectionContainerImage', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSelectionContainerImageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSelectionContainerImageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Selection\Model\SelectionContainerImageQuery) {
            return $criteria;
        }
        $query = new \Selection\Model\SelectionContainerImageQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildSelectionContainerImage|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SelectionContainerImageTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SelectionContainerImageTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildSelectionContainerImage A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, SELECTION_CONTAINER_ID, FILE, VISIBLE, POSITION, CREATED_AT, UPDATED_AT FROM selection_container_image WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildSelectionContainerImage();
            $obj->hydrate($row);
            SelectionContainerImageTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildSelectionContainerImage|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SelectionContainerImageTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SelectionContainerImageTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerImageTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the selection_container_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySelectionContainerId(1234); // WHERE selection_container_id = 1234
     * $query->filterBySelectionContainerId(array(12, 34)); // WHERE selection_container_id IN (12, 34)
     * $query->filterBySelectionContainerId(array('min' => 12)); // WHERE selection_container_id > 12
     * </code>
     *
     * @see       filterBySelectionContainer()
     *
     * @param     mixed $selectionContainerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterBySelectionContainerId($selectionContainerId = null, $comparison = null)
    {
        if (is_array($selectionContainerId)) {
            $useMinMax = false;
            if (isset($selectionContainerId['min'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::SELECTION_CONTAINER_ID, $selectionContainerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($selectionContainerId['max'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::SELECTION_CONTAINER_ID, $selectionContainerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerImageTableMap::SELECTION_CONTAINER_ID, $selectionContainerId, $comparison);
    }

    /**
     * Filter the query on the file column
     *
     * Example usage:
     * <code>
     * $query->filterByFile('fooValue');   // WHERE file = 'fooValue'
     * $query->filterByFile('%fooValue%'); // WHERE file LIKE '%fooValue%'
     * </code>
     *
     * @param     string $file The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterByFile($file = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($file)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $file)) {
                $file = str_replace('*', '%', $file);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SelectionContainerImageTableMap::FILE, $file, $comparison);
    }

    /**
     * Filter the query on the visible column
     *
     * Example usage:
     * <code>
     * $query->filterByVisible(1234); // WHERE visible = 1234
     * $query->filterByVisible(array(12, 34)); // WHERE visible IN (12, 34)
     * $query->filterByVisible(array('min' => 12)); // WHERE visible > 12
     * </code>
     *
     * @param     mixed $visible The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerImageTableMap::VISIBLE, $visible, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByPosition(1234); // WHERE position = 1234
     * $query->filterByPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByPosition(array('min' => 12)); // WHERE position > 12
     * </code>
     *
     * @param     mixed $position The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerImageTableMap::POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerImageTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(SelectionContainerImageTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerImageTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionContainer object
     *
     * @param \Selection\Model\SelectionContainer|ObjectCollection $selectionContainer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterBySelectionContainer($selectionContainer, $comparison = null)
    {
        if ($selectionContainer instanceof \Selection\Model\SelectionContainer) {
            return $this
                ->addUsingAlias(SelectionContainerImageTableMap::SELECTION_CONTAINER_ID, $selectionContainer->getId(), $comparison);
        } elseif ($selectionContainer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SelectionContainerImageTableMap::SELECTION_CONTAINER_ID, $selectionContainer->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySelectionContainer() only accepts arguments of type \Selection\Model\SelectionContainer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SelectionContainer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function joinSelectionContainer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SelectionContainer');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'SelectionContainer');
        }

        return $this;
    }

    /**
     * Use the SelectionContainer relation SelectionContainer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionContainerQuery A secondary query class using the current class as primary query
     */
    public function useSelectionContainerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSelectionContainer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionContainer', '\Selection\Model\SelectionContainerQuery');
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionContainerImageI18n object
     *
     * @param \Selection\Model\SelectionContainerImageI18n|ObjectCollection $selectionContainerImageI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function filterBySelectionContainerImageI18n($selectionContainerImageI18n, $comparison = null)
    {
        if ($selectionContainerImageI18n instanceof \Selection\Model\SelectionContainerImageI18n) {
            return $this
                ->addUsingAlias(SelectionContainerImageTableMap::ID, $selectionContainerImageI18n->getId(), $comparison);
        } elseif ($selectionContainerImageI18n instanceof ObjectCollection) {
            return $this
                ->useSelectionContainerImageI18nQuery()
                ->filterByPrimaryKeys($selectionContainerImageI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySelectionContainerImageI18n() only accepts arguments of type \Selection\Model\SelectionContainerImageI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SelectionContainerImageI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function joinSelectionContainerImageI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SelectionContainerImageI18n');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'SelectionContainerImageI18n');
        }

        return $this;
    }

    /**
     * Use the SelectionContainerImageI18n relation SelectionContainerImageI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionContainerImageI18nQuery A secondary query class using the current class as primary query
     */
    public function useSelectionContainerImageI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinSelectionContainerImageI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionContainerImageI18n', '\Selection\Model\SelectionContainerImageI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSelectionContainerImage $selectionContainerImage Object to remove from the list of results
     *
     * @return ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function prune($selectionContainerImage = null)
    {
        if ($selectionContainerImage) {
            $this->addUsingAlias(SelectionContainerImageTableMap::ID, $selectionContainerImage->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the selection_container_image table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionContainerImageTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SelectionContainerImageTableMap::clearInstancePool();
            SelectionContainerImageTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSelectionContainerImage or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSelectionContainerImage object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionContainerImageTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SelectionContainerImageTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        SelectionContainerImageTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SelectionContainerImageTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionContainerImageTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionContainerImageTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionContainerImageTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionContainerImageTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionContainerImageTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionContainerImageTableMap::CREATED_AT);
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'SelectionContainerImageI18n';

        return $this
            ->joinSelectionContainerImageI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSelectionContainerImageQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('SelectionContainerImageI18n');
        $this->with['SelectionContainerImageI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSelectionContainerImageI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionContainerImageI18n', '\Selection\Model\SelectionContainerImageI18nQuery');
    }

} // SelectionContainerImageQuery
