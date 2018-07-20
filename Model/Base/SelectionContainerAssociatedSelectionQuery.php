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
use Selection\Model\SelectionContainerAssociatedSelection as ChildSelectionContainerAssociatedSelection;
use Selection\Model\SelectionContainerAssociatedSelectionQuery as ChildSelectionContainerAssociatedSelectionQuery;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;

/**
 * Base class that represents a query for the 'selection_container_associated_selection' table.
 *
 *
 *
 * @method     ChildSelectionContainerAssociatedSelectionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSelectionContainerAssociatedSelectionQuery orderBySelectionContainerId($order = Criteria::ASC) Order by the selection_container_id column
 * @method     ChildSelectionContainerAssociatedSelectionQuery orderBySelectionId($order = Criteria::ASC) Order by the selection_id column
 * @method     ChildSelectionContainerAssociatedSelectionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildSelectionContainerAssociatedSelectionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildSelectionContainerAssociatedSelectionQuery groupById() Group by the id column
 * @method     ChildSelectionContainerAssociatedSelectionQuery groupBySelectionContainerId() Group by the selection_container_id column
 * @method     ChildSelectionContainerAssociatedSelectionQuery groupBySelectionId() Group by the selection_id column
 * @method     ChildSelectionContainerAssociatedSelectionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildSelectionContainerAssociatedSelectionQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildSelectionContainerAssociatedSelectionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSelectionContainerAssociatedSelectionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSelectionContainerAssociatedSelectionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSelectionContainerAssociatedSelectionQuery leftJoinSelectionContainer($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionContainer relation
 * @method     ChildSelectionContainerAssociatedSelectionQuery rightJoinSelectionContainer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionContainer relation
 * @method     ChildSelectionContainerAssociatedSelectionQuery innerJoinSelectionContainer($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionContainer relation
 *
 * @method     ChildSelectionContainerAssociatedSelectionQuery leftJoinSelection($relationAlias = null) Adds a LEFT JOIN clause to the query using the Selection relation
 * @method     ChildSelectionContainerAssociatedSelectionQuery rightJoinSelection($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Selection relation
 * @method     ChildSelectionContainerAssociatedSelectionQuery innerJoinSelection($relationAlias = null) Adds a INNER JOIN clause to the query using the Selection relation
 *
 * @method     ChildSelectionContainerAssociatedSelection findOne(ConnectionInterface $con = null) Return the first ChildSelectionContainerAssociatedSelection matching the query
 * @method     ChildSelectionContainerAssociatedSelection findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSelectionContainerAssociatedSelection matching the query, or a new ChildSelectionContainerAssociatedSelection object populated from the query conditions when no match is found
 *
 * @method     ChildSelectionContainerAssociatedSelection findOneById(int $id) Return the first ChildSelectionContainerAssociatedSelection filtered by the id column
 * @method     ChildSelectionContainerAssociatedSelection findOneBySelectionContainerId(int $selection_container_id) Return the first ChildSelectionContainerAssociatedSelection filtered by the selection_container_id column
 * @method     ChildSelectionContainerAssociatedSelection findOneBySelectionId(int $selection_id) Return the first ChildSelectionContainerAssociatedSelection filtered by the selection_id column
 * @method     ChildSelectionContainerAssociatedSelection findOneByCreatedAt(string $created_at) Return the first ChildSelectionContainerAssociatedSelection filtered by the created_at column
 * @method     ChildSelectionContainerAssociatedSelection findOneByUpdatedAt(string $updated_at) Return the first ChildSelectionContainerAssociatedSelection filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildSelectionContainerAssociatedSelection objects filtered by the id column
 * @method     array findBySelectionContainerId(int $selection_container_id) Return ChildSelectionContainerAssociatedSelection objects filtered by the selection_container_id column
 * @method     array findBySelectionId(int $selection_id) Return ChildSelectionContainerAssociatedSelection objects filtered by the selection_id column
 * @method     array findByCreatedAt(string $created_at) Return ChildSelectionContainerAssociatedSelection objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildSelectionContainerAssociatedSelection objects filtered by the updated_at column
 *
 */
abstract class SelectionContainerAssociatedSelectionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Selection\Model\Base\SelectionContainerAssociatedSelectionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Selection\\Model\\SelectionContainerAssociatedSelection', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSelectionContainerAssociatedSelectionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSelectionContainerAssociatedSelectionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Selection\Model\SelectionContainerAssociatedSelectionQuery) {
            return $criteria;
        }
        $query = new \Selection\Model\SelectionContainerAssociatedSelectionQuery();
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
     * @return ChildSelectionContainerAssociatedSelection|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SelectionContainerAssociatedSelectionTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SelectionContainerAssociatedSelectionTableMap::DATABASE_NAME);
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
     * @return   ChildSelectionContainerAssociatedSelection A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, SELECTION_CONTAINER_ID, SELECTION_ID, CREATED_AT, UPDATED_AT FROM selection_container_associated_selection WHERE ID = :p0';
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
            $obj = new ChildSelectionContainerAssociatedSelection();
            $obj->hydrate($row);
            SelectionContainerAssociatedSelectionTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSelectionContainerAssociatedSelection|array|mixed the result, formatted by the current formatter
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
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::ID, $id, $comparison);
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
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterBySelectionContainerId($selectionContainerId = null, $comparison = null)
    {
        if (is_array($selectionContainerId)) {
            $useMinMax = false;
            if (isset($selectionContainerId['min'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_CONTAINER_ID, $selectionContainerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($selectionContainerId['max'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_CONTAINER_ID, $selectionContainerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_CONTAINER_ID, $selectionContainerId, $comparison);
    }

    /**
     * Filter the query on the selection_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySelectionId(1234); // WHERE selection_id = 1234
     * $query->filterBySelectionId(array(12, 34)); // WHERE selection_id IN (12, 34)
     * $query->filterBySelectionId(array('min' => 12)); // WHERE selection_id > 12
     * </code>
     *
     * @see       filterBySelection()
     *
     * @param     mixed $selectionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterBySelectionId($selectionId = null, $comparison = null)
    {
        if (is_array($selectionId)) {
            $useMinMax = false;
            if (isset($selectionId['min'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID, $selectionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($selectionId['max'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID, $selectionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID, $selectionId, $comparison);
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
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionContainer object
     *
     * @param \Selection\Model\SelectionContainer|ObjectCollection $selectionContainer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterBySelectionContainer($selectionContainer, $comparison = null)
    {
        if ($selectionContainer instanceof \Selection\Model\SelectionContainer) {
            return $this
                ->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_CONTAINER_ID, $selectionContainer->getId(), $comparison);
        } elseif ($selectionContainer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_CONTAINER_ID, $selectionContainer->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
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
     * Filter the query by a related \Selection\Model\Selection object
     *
     * @param \Selection\Model\Selection|ObjectCollection $selection The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function filterBySelection($selection, $comparison = null)
    {
        if ($selection instanceof \Selection\Model\Selection) {
            return $this
                ->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID, $selection->getId(), $comparison);
        } elseif ($selection instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID, $selection->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySelection() only accepts arguments of type \Selection\Model\Selection or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Selection relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function joinSelection($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Selection');

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
            $this->addJoinObject($join, 'Selection');
        }

        return $this;
    }

    /**
     * Use the Selection relation Selection object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionQuery A secondary query class using the current class as primary query
     */
    public function useSelectionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSelection($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Selection', '\Selection\Model\SelectionQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSelectionContainerAssociatedSelection $selectionContainerAssociatedSelection Object to remove from the list of results
     *
     * @return ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function prune($selectionContainerAssociatedSelection = null)
    {
        if ($selectionContainerAssociatedSelection) {
            $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::ID, $selectionContainerAssociatedSelection->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the selection_container_associated_selection table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionContainerAssociatedSelectionTableMap::DATABASE_NAME);
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
            SelectionContainerAssociatedSelectionTableMap::clearInstancePool();
            SelectionContainerAssociatedSelectionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSelectionContainerAssociatedSelection or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSelectionContainerAssociatedSelection object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionContainerAssociatedSelectionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SelectionContainerAssociatedSelectionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        SelectionContainerAssociatedSelectionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SelectionContainerAssociatedSelectionTableMap::clearRelatedInstancePool();
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
     * @return     ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionContainerAssociatedSelectionTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionContainerAssociatedSelectionTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionContainerAssociatedSelectionTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionContainerAssociatedSelectionTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildSelectionContainerAssociatedSelectionQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionContainerAssociatedSelectionTableMap::CREATED_AT);
    }

} // SelectionContainerAssociatedSelectionQuery
