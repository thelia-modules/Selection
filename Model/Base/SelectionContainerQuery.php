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
use Selection\Model\SelectionContainer as ChildSelectionContainer;
use Selection\Model\SelectionContainerI18nQuery as ChildSelectionContainerI18nQuery;
use Selection\Model\SelectionContainerQuery as ChildSelectionContainerQuery;
use Selection\Model\Map\SelectionContainerTableMap;

/**
 * Base class that represents a query for the 'selection_container' table.
 *
 *
 *
 * @method     ChildSelectionContainerQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSelectionContainerQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildSelectionContainerQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildSelectionContainerQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildSelectionContainerQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildSelectionContainerQuery groupById() Group by the id column
 * @method     ChildSelectionContainerQuery groupByVisible() Group by the visible column
 * @method     ChildSelectionContainerQuery groupByPosition() Group by the position column
 * @method     ChildSelectionContainerQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildSelectionContainerQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildSelectionContainerQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSelectionContainerQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSelectionContainerQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSelectionContainerQuery leftJoinSelectionContainerAssociatedSelection($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionContainerAssociatedSelection relation
 * @method     ChildSelectionContainerQuery rightJoinSelectionContainerAssociatedSelection($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionContainerAssociatedSelection relation
 * @method     ChildSelectionContainerQuery innerJoinSelectionContainerAssociatedSelection($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionContainerAssociatedSelection relation
 *
 * @method     ChildSelectionContainerQuery leftJoinSelectionContainerImage($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionContainerImage relation
 * @method     ChildSelectionContainerQuery rightJoinSelectionContainerImage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionContainerImage relation
 * @method     ChildSelectionContainerQuery innerJoinSelectionContainerImage($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionContainerImage relation
 *
 * @method     ChildSelectionContainerQuery leftJoinSelectionContainerI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the SelectionContainerI18n relation
 * @method     ChildSelectionContainerQuery rightJoinSelectionContainerI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SelectionContainerI18n relation
 * @method     ChildSelectionContainerQuery innerJoinSelectionContainerI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the SelectionContainerI18n relation
 *
 * @method     ChildSelectionContainer findOne(ConnectionInterface $con = null) Return the first ChildSelectionContainer matching the query
 * @method     ChildSelectionContainer findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSelectionContainer matching the query, or a new ChildSelectionContainer object populated from the query conditions when no match is found
 *
 * @method     ChildSelectionContainer findOneById(int $id) Return the first ChildSelectionContainer filtered by the id column
 * @method     ChildSelectionContainer findOneByVisible(int $visible) Return the first ChildSelectionContainer filtered by the visible column
 * @method     ChildSelectionContainer findOneByPosition(int $position) Return the first ChildSelectionContainer filtered by the position column
 * @method     ChildSelectionContainer findOneByCreatedAt(string $created_at) Return the first ChildSelectionContainer filtered by the created_at column
 * @method     ChildSelectionContainer findOneByUpdatedAt(string $updated_at) Return the first ChildSelectionContainer filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildSelectionContainer objects filtered by the id column
 * @method     array findByVisible(int $visible) Return ChildSelectionContainer objects filtered by the visible column
 * @method     array findByPosition(int $position) Return ChildSelectionContainer objects filtered by the position column
 * @method     array findByCreatedAt(string $created_at) Return ChildSelectionContainer objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildSelectionContainer objects filtered by the updated_at column
 *
 */
abstract class SelectionContainerQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Selection\Model\Base\SelectionContainerQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Selection\\Model\\SelectionContainer', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSelectionContainerQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSelectionContainerQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Selection\Model\SelectionContainerQuery) {
            return $criteria;
        }
        $query = new \Selection\Model\SelectionContainerQuery();
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
     * @return ChildSelectionContainer|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SelectionContainerTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SelectionContainerTableMap::DATABASE_NAME);
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
     * @return   ChildSelectionContainer A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, VISIBLE, POSITION, CREATED_AT, UPDATED_AT FROM selection_container WHERE ID = :p0';
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
            $obj = new ChildSelectionContainer();
            $obj->hydrate($row);
            SelectionContainerTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSelectionContainer|array|mixed the result, formatted by the current formatter
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
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SelectionContainerTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SelectionContainerTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SelectionContainerTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SelectionContainerTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerTableMap::ID, $id, $comparison);
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
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(SelectionContainerTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(SelectionContainerTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerTableMap::VISIBLE, $visible, $comparison);
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
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(SelectionContainerTableMap::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(SelectionContainerTableMap::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerTableMap::POSITION, $position, $comparison);
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
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(SelectionContainerTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(SelectionContainerTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(SelectionContainerTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(SelectionContainerTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SelectionContainerTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionContainerAssociatedSelection object
     *
     * @param \Selection\Model\SelectionContainerAssociatedSelection|ObjectCollection $selectionContainerAssociatedSelection  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterBySelectionContainerAssociatedSelection($selectionContainerAssociatedSelection, $comparison = null)
    {
        if ($selectionContainerAssociatedSelection instanceof \Selection\Model\SelectionContainerAssociatedSelection) {
            return $this
                ->addUsingAlias(SelectionContainerTableMap::ID, $selectionContainerAssociatedSelection->getSelectionContainerId(), $comparison);
        } elseif ($selectionContainerAssociatedSelection instanceof ObjectCollection) {
            return $this
                ->useSelectionContainerAssociatedSelectionQuery()
                ->filterByPrimaryKeys($selectionContainerAssociatedSelection->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySelectionContainerAssociatedSelection() only accepts arguments of type \Selection\Model\SelectionContainerAssociatedSelection or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SelectionContainerAssociatedSelection relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function joinSelectionContainerAssociatedSelection($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SelectionContainerAssociatedSelection');

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
            $this->addJoinObject($join, 'SelectionContainerAssociatedSelection');
        }

        return $this;
    }

    /**
     * Use the SelectionContainerAssociatedSelection relation SelectionContainerAssociatedSelection object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionContainerAssociatedSelectionQuery A secondary query class using the current class as primary query
     */
    public function useSelectionContainerAssociatedSelectionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSelectionContainerAssociatedSelection($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionContainerAssociatedSelection', '\Selection\Model\SelectionContainerAssociatedSelectionQuery');
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionContainerImage object
     *
     * @param \Selection\Model\SelectionContainerImage|ObjectCollection $selectionContainerImage  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterBySelectionContainerImage($selectionContainerImage, $comparison = null)
    {
        if ($selectionContainerImage instanceof \Selection\Model\SelectionContainerImage) {
            return $this
                ->addUsingAlias(SelectionContainerTableMap::ID, $selectionContainerImage->getSelectionContainerId(), $comparison);
        } elseif ($selectionContainerImage instanceof ObjectCollection) {
            return $this
                ->useSelectionContainerImageQuery()
                ->filterByPrimaryKeys($selectionContainerImage->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySelectionContainerImage() only accepts arguments of type \Selection\Model\SelectionContainerImage or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SelectionContainerImage relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function joinSelectionContainerImage($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SelectionContainerImage');

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
            $this->addJoinObject($join, 'SelectionContainerImage');
        }

        return $this;
    }

    /**
     * Use the SelectionContainerImage relation SelectionContainerImage object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionContainerImageQuery A secondary query class using the current class as primary query
     */
    public function useSelectionContainerImageQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSelectionContainerImage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionContainerImage', '\Selection\Model\SelectionContainerImageQuery');
    }

    /**
     * Filter the query by a related \Selection\Model\SelectionContainerI18n object
     *
     * @param \Selection\Model\SelectionContainerI18n|ObjectCollection $selectionContainerI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function filterBySelectionContainerI18n($selectionContainerI18n, $comparison = null)
    {
        if ($selectionContainerI18n instanceof \Selection\Model\SelectionContainerI18n) {
            return $this
                ->addUsingAlias(SelectionContainerTableMap::ID, $selectionContainerI18n->getId(), $comparison);
        } elseif ($selectionContainerI18n instanceof ObjectCollection) {
            return $this
                ->useSelectionContainerI18nQuery()
                ->filterByPrimaryKeys($selectionContainerI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySelectionContainerI18n() only accepts arguments of type \Selection\Model\SelectionContainerI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SelectionContainerI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function joinSelectionContainerI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SelectionContainerI18n');

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
            $this->addJoinObject($join, 'SelectionContainerI18n');
        }

        return $this;
    }

    /**
     * Use the SelectionContainerI18n relation SelectionContainerI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Selection\Model\SelectionContainerI18nQuery A secondary query class using the current class as primary query
     */
    public function useSelectionContainerI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinSelectionContainerI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionContainerI18n', '\Selection\Model\SelectionContainerI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSelectionContainer $selectionContainer Object to remove from the list of results
     *
     * @return ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function prune($selectionContainer = null)
    {
        if ($selectionContainer) {
            $this->addUsingAlias(SelectionContainerTableMap::ID, $selectionContainer->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the selection_container table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionContainerTableMap::DATABASE_NAME);
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
            SelectionContainerTableMap::clearInstancePool();
            SelectionContainerTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSelectionContainer or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSelectionContainer object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionContainerTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SelectionContainerTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        SelectionContainerTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SelectionContainerTableMap::clearRelatedInstancePool();
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
     * @return     ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionContainerTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SelectionContainerTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionContainerTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionContainerTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SelectionContainerTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SelectionContainerTableMap::CREATED_AT);
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'SelectionContainerI18n';

        return $this
            ->joinSelectionContainerI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildSelectionContainerQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('SelectionContainerI18n');
        $this->with['SelectionContainerI18n']->setIsWithOneToMany(false);

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
     * @return    ChildSelectionContainerI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SelectionContainerI18n', '\Selection\Model\SelectionContainerI18nQuery');
    }

} // SelectionContainerQuery
