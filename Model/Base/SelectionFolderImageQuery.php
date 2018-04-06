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
use Selection\Model\SelectionFolderImage as ChildSelectionFolderImage;
use Selection\Model\SelectionFolderImageI18nQuery as ChildSelectionFolderImageI18nQuery;
use Selection\Model\SelectionFolderImageQuery as ChildSelectionFolderImageQuery;
use Selection\Model\Map\SelectionFolderImageTableMap;

/**
 * Base class that represents a query for the 'selection_folder_image' table.
 *
 *
 *
 * @method     ChildSelectionFolderImageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSelectionFolderImageQuery orderBySelectionFolderId($order = Criteria::ASC) Order by the selection_folder_id column
 * @method     ChildSelectionFolderImageQuery orderByFile($order = Criteria::ASC) Order by the file column
 * @method     ChildSelectionFolderImageQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildSelectionFolderImageQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildSelectionFolderImageQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildSelectionFolderImageQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildSelectionFolderImageQuery groupById() Group by the id column
 * @method     ChildSelectionFolderImageQuery groupBySelectionFolderId() Group by the selection_folder_id column
 * @method     ChildSelectionFolderImageQuery groupByFile() Group by the file column
 * @method     ChildSelectionFolderImageQuery groupByVisible() Group by the visible column
 * @method     ChildSelectionFolderImageQuery groupByPosition() Group by the position column
 * @method     ChildSelectionFolderImageQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildSelectionFolderImageQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildSelectionFolderImageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSelectionFolderImageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSelectionFolderImageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSelectionFolderImageQuery leftJoinSelectionFolder($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionFolder relation
 * @method     ChildSelectionFolderImageQuery rightJoinSelectionFolder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionFolder relation
 * @method     ChildSelectionFolderImageQuery innerJoinSelectionFolder($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionFolder relation
 *
 * @method     ChildSelectionFolderImageQuery leftJoinSelectionFolderImageI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionFolderImageI18n relation
 * @method     ChildSelectionFolderImageQuery rightJoinSelectionFolderImageI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionFolderImageI18n relation
 * @method     ChildSelectionFolderImageQuery innerJoinSelectionFolderImageI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionFolderImageI18n relation
 *
 * @method     ChildSelectionFolderImage findOne(ConnectionInterface $con = null) Return the first ChildSelectionFolderImage matching the query
 * @method     ChildSelectionFolderImage findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSelectionFolderImage matching the query, or a new ChildSelectionFolderImage object populated from the query conditions when no match is found
 *
 * @method     ChildSelectionFolderImage findOneById(int $id) Return the first ChildSelectionFolderImage filtered by the id column
 * @method     ChildSelectionFolderImage findOneBySelectionFolderId(int $selection_folder_id) Return the first ChildSelectionFolderImage filtered by the selection_folder_id column
 * @method     ChildSelectionFolderImage findOneByFile(string $file) Return the first ChildSelectionFolderImage filtered by the file column
 * @method     ChildSelectionFolderImage findOneByVisible(int $visible) Return the first ChildSelectionFolderImage filtered by the visible column
 * @method     ChildSelectionFolderImage findOneByPosition(int $position) Return the first ChildSelectionFolderImage filtered by the position column
 * @method     ChildSelectionFolderImage findOneByCreatedAt(string $created_at) Return the first ChildSelectionFolderImage filtered by the created_at column
 * @method     ChildSelectionFolderImage findOneByUpdatedAt(string $updated_at) Return the first ChildSelectionFolderImage filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildSelectionFolderImage objects filtered by the id column
 * @method     array findBySelectionFolderId(int $selection_folder_id) Return ChildSelectionFolderImage objects filtered by the selection_folder_id column
 * @method     array findByFile(string $file) Return ChildSelectionFolderImage objects filtered by the file column
 * @method     array findByVisible(int $visible) Return ChildSelectionFolderImage objects filtered by the visible column
 * @method     array findByPosition(int $position) Return ChildSelectionFolderImage objects filtered by the position column
 * @method     array findByCreatedAt(string $created_at) Return ChildSelectionFolderImage objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildSelectionFolderImage objects filtered by the updated_at column
 *
 */
abstract class SelectionFolderImageQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Selection\Model\Base\SelectionFolderImageQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Selection\\Model\\SelectionFolderImage', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSelectionFolderImageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSelectionFolderImageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Selection\Model\SelectionFolderImageQuery) {
            return $criteria;
        }
        $query = new \Selection\Model\SelectionFolderImageQuery();
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
     * @return ChildSelectionFolderImage|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SelectionFolderImageTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SelectionFolderImageTableMap::DATABASE_NAME);
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
     * @return   ChildSelectionFolderImage A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, SELECTION_FOLDER_ID, FILE, VISIBLE, POSITION, CREATED_AT, UPDATED_AT FROM selection_folder_image WHERE ID = :p0';
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
            $obj = new ChildSelectionFolderImage();
            $obj->hydrate($row);
            SelectionFolderImageTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSelectionFolderImage|array|mixed the result, formatted by the current formatter
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
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SelectionFolderImageTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SelectionFolderImageTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionFolderImageTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the selection_folder_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySelectionFolderId(1234); // WHERE selection_folder_id = 1234
     * $query->filterBySelectionFolderId(array(12, 34)); // WHERE selection_folder_id IN (12, 34)
     * $query->filterBySelectionFolderId(array('min' => 12)); // WHERE selection_folder_id > 12
     * </code>
     *
     * @see       filterBySelectionFolder()
     *
     * @param     mixed $selectionFolderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterBySelectionFolderId($selectionFolderId = null, $comparison = null)
    {
        if (is_array($selectionFolderId)) {
            $useMinMax = false;
            if (isset($selectionFolderId['min'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::SELECTION_FOLDER_ID, $selectionFolderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($selectionFolderId['max'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::SELECTION_FOLDER_ID, $selectionFolderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionFolderImageTableMap::SELECTION_FOLDER_ID, $selectionFolderId, $comparison);
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
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SelectionFolderImageTableMap::FILE, $file, $comparison);
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
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionFolderImageTableMap::VISIBLE, $visible, $comparison);
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
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionFolderImageTableMap::POSITION, $position, $comparison);
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
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionFolderImageTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(SelectionFolderImageTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionFolderImageTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionFolder object
     *
     * @param \Selection\Model\SelectionFolder|ObjectCollection $selectionFolder The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterBySelectionFolder($selectionFolder, $comparison = null)
    {
        if ($selectionFolder instanceof \Selection\Model\SelectionFolder) {
            return $this
                ->addUsingAlias(SelectionFolderImageTableMap::SELECTION_FOLDER_ID, $selectionFolder->getId(), $comparison);
        } elseif ($selectionFolder instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SelectionFolderImageTableMap::SELECTION_FOLDER_ID, $selectionFolder->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySelectionFolder() only accepts arguments of type \Selection\Model\SelectionFolder or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SelectionFolder relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function joinSelectionFolder($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SelectionFolder');

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
            $this->addJoinObject($join, 'SelectionFolder');
        }

        return $this;
    }

    /**
     * Use the SelectionFolder relation SelectionFolder object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionFolderQuery A secondary query class using the current class as primary query
     */
    public function useSelectionFolderQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSelectionFolder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionFolder', '\Selection\Model\SelectionFolderQuery');
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionFolderImageI18n object
     *
     * @param \Selection\Model\SelectionFolderImageI18n|ObjectCollection $selectionFolderImageI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function filterBySelectionFolderImageI18n($selectionFolderImageI18n, $comparison = null)
    {
        if ($selectionFolderImageI18n instanceof \Selection\Model\SelectionFolderImageI18n) {
            return $this
                ->addUsingAlias(SelectionFolderImageTableMap::ID, $selectionFolderImageI18n->getId(), $comparison);
        } elseif ($selectionFolderImageI18n instanceof ObjectCollection) {
            return $this
                ->useSelectionFolderImageI18nQuery()
                ->filterByPrimaryKeys($selectionFolderImageI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySelectionFolderImageI18n() only accepts arguments of type \Selection\Model\SelectionFolderImageI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SelectionFolderImageI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function joinSelectionFolderImageI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SelectionFolderImageI18n');

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
            $this->addJoinObject($join, 'SelectionFolderImageI18n');
        }

        return $this;
    }

    /**
     * Use the SelectionFolderImageI18n relation SelectionFolderImageI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionFolderImageI18nQuery A secondary query class using the current class as primary query
     */
    public function useSelectionFolderImageI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinSelectionFolderImageI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionFolderImageI18n', '\Selection\Model\SelectionFolderImageI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSelectionFolderImage $selectionFolderImage Object to remove from the list of results
     *
     * @return ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function prune($selectionFolderImage = null)
    {
        if ($selectionFolderImage) {
            $this->addUsingAlias(SelectionFolderImageTableMap::ID, $selectionFolderImage->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the selection_folder_image table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionFolderImageTableMap::DATABASE_NAME);
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
            SelectionFolderImageTableMap::clearInstancePool();
            SelectionFolderImageTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSelectionFolderImage or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSelectionFolderImage object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionFolderImageTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SelectionFolderImageTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        SelectionFolderImageTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SelectionFolderImageTableMap::clearRelatedInstancePool();
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
     * @return     ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionFolderImageTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionFolderImageTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionFolderImageTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionFolderImageTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionFolderImageTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionFolderImageTableMap::CREATED_AT);
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'SelectionFolderImageI18n';

        return $this
            ->joinSelectionFolderImageI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSelectionFolderImageQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('SelectionFolderImageI18n');
        $this->with['SelectionFolderImageI18n']->setIsWithOneToMany(false);

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
     * @return    ChildSelectionFolderImageI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionFolderImageI18n', '\Selection\Model\SelectionFolderImageI18nQuery');
    }

} // SelectionFolderImageQuery
