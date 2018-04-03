<?php

namespace Selection\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;
use Selection\Model\Selection as ChildSelection;
use Selection\Model\SelectionContent as ChildSelectionContent;
use Selection\Model\SelectionContentQuery as ChildSelectionContentQuery;
use Selection\Model\SelectionI18n as ChildSelectionI18n;
use Selection\Model\SelectionI18nQuery as ChildSelectionI18nQuery;
use Selection\Model\SelectionImage as ChildSelectionImage;
use Selection\Model\SelectionImageQuery as ChildSelectionImageQuery;
use Selection\Model\SelectionProduct as ChildSelectionProduct;
use Selection\Model\SelectionProductQuery as ChildSelectionProductQuery;
use Selection\Model\SelectionQuery as ChildSelectionQuery;
use Selection\Model\Map\SelectionTableMap;

abstract class Selection implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Selection\\Model\\Map\\SelectionTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the visible field.
     * @var        int
     */
    protected $visible;

    /**
     * The value for the position field.
     * @var        int
     */
    protected $position;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        ObjectCollection|ChildSelectionProduct[] Collection to store aggregation of ChildSelectionProduct objects.
     */
    protected $collSelectionProducts;
    protected $collSelectionProductsPartial;

    /**
     * @var        ObjectCollection|ChildSelectionContent[] Collection to store aggregation of ChildSelectionContent objects.
     */
    protected $collSelectionContents;
    protected $collSelectionContentsPartial;

    /**
     * @var        ObjectCollection|ChildSelectionImage[] Collection to store aggregation of ChildSelectionImage objects.
     */
    protected $collSelectionImages;
    protected $collSelectionImagesPartial;

    /**
     * @var        ObjectCollection|ChildSelectionI18n[] Collection to store aggregation of ChildSelectionI18n objects.
     */
    protected $collSelectionI18ns;
    protected $collSelectionI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';

    /**
     * Current translation objects
     * @var        array[ChildSelectionI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $selectionProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $selectionContentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $selectionImagesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $selectionI18nsScheduledForDeletion = null;

    /**
     * Initializes internal state of Selection\Model\Base\Selection object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Selection</code> instance.  If
     * <code>obj</code> is an instance of <code>Selection</code>, delegates to
     * <code>equals(Selection)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Selection The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Selection The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [visible] column value.
     *
     * @return   int
     */
    public function getVisible()
    {

        return $this->visible;
    }

    /**
     * Get the [position] column value.
     *
     * @return   int
     */
    public function getPosition()
    {

        return $this->position;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[SelectionTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [visible] column.
     *
     * @param      int $v new value
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function setVisible($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->visible !== $v) {
            $this->visible = $v;
            $this->modifiedColumns[SelectionTableMap::VISIBLE] = true;
        }


        return $this;
    } // setVisible()

    /**
     * Set the value of [position] column.
     *
     * @param      int $v new value
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function setPosition($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->position !== $v) {
            $this->position = $v;
            $this->modifiedColumns[SelectionTableMap::POSITION] = true;
        }


        return $this;
    } // setPosition()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[SelectionTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[SelectionTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : SelectionTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : SelectionTableMap::translateFieldName('Visible', TableMap::TYPE_PHPNAME, $indexType)];
            $this->visible = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : SelectionTableMap::translateFieldName('Position', TableMap::TYPE_PHPNAME, $indexType)];
            $this->position = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : SelectionTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : SelectionTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = SelectionTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Selection\Model\Selection object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SelectionTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildSelectionQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collSelectionProducts = null;

            $this->collSelectionContents = null;

            $this->collSelectionImages = null;

            $this->collSelectionI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Selection::setDeleted()
     * @see Selection::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildSelectionQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(SelectionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(SelectionTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(SelectionTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(SelectionTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                SelectionTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->selectionProductsScheduledForDeletion !== null) {
                if (!$this->selectionProductsScheduledForDeletion->isEmpty()) {
                    \Selection\Model\SelectionProductQuery::create()
                        ->filterByPrimaryKeys($this->selectionProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->selectionProductsScheduledForDeletion = null;
                }
            }

                if ($this->collSelectionProducts !== null) {
            foreach ($this->collSelectionProducts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->selectionContentsScheduledForDeletion !== null) {
                if (!$this->selectionContentsScheduledForDeletion->isEmpty()) {
                    \Selection\Model\SelectionContentQuery::create()
                        ->filterByPrimaryKeys($this->selectionContentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->selectionContentsScheduledForDeletion = null;
                }
            }

                if ($this->collSelectionContents !== null) {
            foreach ($this->collSelectionContents as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->selectionImagesScheduledForDeletion !== null) {
                if (!$this->selectionImagesScheduledForDeletion->isEmpty()) {
                    \Selection\Model\SelectionImageQuery::create()
                        ->filterByPrimaryKeys($this->selectionImagesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->selectionImagesScheduledForDeletion = null;
                }
            }

                if ($this->collSelectionImages !== null) {
            foreach ($this->collSelectionImages as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->selectionI18nsScheduledForDeletion !== null) {
                if (!$this->selectionI18nsScheduledForDeletion->isEmpty()) {
                    \Selection\Model\SelectionI18nQuery::create()
                        ->filterByPrimaryKeys($this->selectionI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->selectionI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collSelectionI18ns !== null) {
            foreach ($this->collSelectionI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[SelectionTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SelectionTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SelectionTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(SelectionTableMap::VISIBLE)) {
            $modifiedColumns[':p' . $index++]  = 'VISIBLE';
        }
        if ($this->isColumnModified(SelectionTableMap::POSITION)) {
            $modifiedColumns[':p' . $index++]  = 'POSITION';
        }
        if ($this->isColumnModified(SelectionTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(SelectionTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO selection (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'VISIBLE':
                        $stmt->bindValue($identifier, $this->visible, PDO::PARAM_INT);
                        break;
                    case 'POSITION':
                        $stmt->bindValue($identifier, $this->position, PDO::PARAM_INT);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = SelectionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getVisible();
                break;
            case 2:
                return $this->getPosition();
                break;
            case 3:
                return $this->getCreatedAt();
                break;
            case 4:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Selection'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Selection'][$this->getPrimaryKey()] = true;
        $keys = SelectionTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getVisible(),
            $keys[2] => $this->getPosition(),
            $keys[3] => $this->getCreatedAt(),
            $keys[4] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collSelectionProducts) {
                $result['SelectionProducts'] = $this->collSelectionProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSelectionContents) {
                $result['SelectionContents'] = $this->collSelectionContents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSelectionImages) {
                $result['SelectionImages'] = $this->collSelectionImages->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSelectionI18ns) {
                $result['SelectionI18ns'] = $this->collSelectionI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = SelectionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setVisible($value);
                break;
            case 2:
                $this->setPosition($value);
                break;
            case 3:
                $this->setCreatedAt($value);
                break;
            case 4:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = SelectionTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setVisible($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPosition($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCreatedAt($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setUpdatedAt($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SelectionTableMap::DATABASE_NAME);

        if ($this->isColumnModified(SelectionTableMap::ID)) $criteria->add(SelectionTableMap::ID, $this->id);
        if ($this->isColumnModified(SelectionTableMap::VISIBLE)) $criteria->add(SelectionTableMap::VISIBLE, $this->visible);
        if ($this->isColumnModified(SelectionTableMap::POSITION)) $criteria->add(SelectionTableMap::POSITION, $this->position);
        if ($this->isColumnModified(SelectionTableMap::CREATED_AT)) $criteria->add(SelectionTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(SelectionTableMap::UPDATED_AT)) $criteria->add(SelectionTableMap::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(SelectionTableMap::DATABASE_NAME);
        $criteria->add(SelectionTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Selection\Model\Selection (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setVisible($this->getVisible());
        $copyObj->setPosition($this->getPosition());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getSelectionProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSelectionProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSelectionContents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSelectionContent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSelectionImages() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSelectionImage($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSelectionI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSelectionI18n($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \Selection\Model\Selection Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('SelectionProduct' == $relationName) {
            return $this->initSelectionProducts();
        }
        if ('SelectionContent' == $relationName) {
            return $this->initSelectionContents();
        }
        if ('SelectionImage' == $relationName) {
            return $this->initSelectionImages();
        }
        if ('SelectionI18n' == $relationName) {
            return $this->initSelectionI18ns();
        }
    }

    /**
     * Clears out the collSelectionProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSelectionProducts()
     */
    public function clearSelectionProducts()
    {
        $this->collSelectionProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSelectionProducts collection loaded partially.
     */
    public function resetPartialSelectionProducts($v = true)
    {
        $this->collSelectionProductsPartial = $v;
    }

    /**
     * Initializes the collSelectionProducts collection.
     *
     * By default this just sets the collSelectionProducts collection to an empty array (like clearcollSelectionProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSelectionProducts($overrideExisting = true)
    {
        if (null !== $this->collSelectionProducts && !$overrideExisting) {
            return;
        }
        $this->collSelectionProducts = new ObjectCollection();
        $this->collSelectionProducts->setModel('\Selection\Model\SelectionProduct');
    }

    /**
     * Gets an array of ChildSelectionProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSelection is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildSelectionProduct[] List of ChildSelectionProduct objects
     * @throws PropelException
     */
    public function getSelectionProducts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionProductsPartial && !$this->isNew();
        if (null === $this->collSelectionProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSelectionProducts) {
                // return empty collection
                $this->initSelectionProducts();
            } else {
                $collSelectionProducts = ChildSelectionProductQuery::create(null, $criteria)
                    ->filterBySelection($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSelectionProductsPartial && count($collSelectionProducts)) {
                        $this->initSelectionProducts(false);

                        foreach ($collSelectionProducts as $obj) {
                            if (false == $this->collSelectionProducts->contains($obj)) {
                                $this->collSelectionProducts->append($obj);
                            }
                        }

                        $this->collSelectionProductsPartial = true;
                    }

                    reset($collSelectionProducts);

                    return $collSelectionProducts;
                }

                if ($partial && $this->collSelectionProducts) {
                    foreach ($this->collSelectionProducts as $obj) {
                        if ($obj->isNew()) {
                            $collSelectionProducts[] = $obj;
                        }
                    }
                }

                $this->collSelectionProducts = $collSelectionProducts;
                $this->collSelectionProductsPartial = false;
            }
        }

        return $this->collSelectionProducts;
    }

    /**
     * Sets a collection of SelectionProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $selectionProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildSelection The current object (for fluent API support)
     */
    public function setSelectionProducts(Collection $selectionProducts, ConnectionInterface $con = null)
    {
        $selectionProductsToDelete = $this->getSelectionProducts(new Criteria(), $con)->diff($selectionProducts);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->selectionProductsScheduledForDeletion = clone $selectionProductsToDelete;

        foreach ($selectionProductsToDelete as $selectionProductRemoved) {
            $selectionProductRemoved->setSelection(null);
        }

        $this->collSelectionProducts = null;
        foreach ($selectionProducts as $selectionProduct) {
            $this->addSelectionProduct($selectionProduct);
        }

        $this->collSelectionProducts = $selectionProducts;
        $this->collSelectionProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SelectionProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SelectionProduct objects.
     * @throws PropelException
     */
    public function countSelectionProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionProductsPartial && !$this->isNew();
        if (null === $this->collSelectionProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSelectionProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSelectionProducts());
            }

            $query = ChildSelectionProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySelection($this)
                ->count($con);
        }

        return count($this->collSelectionProducts);
    }

    /**
     * Method called to associate a ChildSelectionProduct object to this object
     * through the ChildSelectionProduct foreign key attribute.
     *
     * @param    ChildSelectionProduct $l ChildSelectionProduct
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function addSelectionProduct(ChildSelectionProduct $l)
    {
        if ($this->collSelectionProducts === null) {
            $this->initSelectionProducts();
            $this->collSelectionProductsPartial = true;
        }

        if (!in_array($l, $this->collSelectionProducts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSelectionProduct($l);
        }

        return $this;
    }

    /**
     * @param SelectionProduct $selectionProduct The selectionProduct object to add.
     */
    protected function doAddSelectionProduct($selectionProduct)
    {
        $this->collSelectionProducts[]= $selectionProduct;
        $selectionProduct->setSelection($this);
    }

    /**
     * @param  SelectionProduct $selectionProduct The selectionProduct object to remove.
     * @return ChildSelection The current object (for fluent API support)
     */
    public function removeSelectionProduct($selectionProduct)
    {
        if ($this->getSelectionProducts()->contains($selectionProduct)) {
            $this->collSelectionProducts->remove($this->collSelectionProducts->search($selectionProduct));
            if (null === $this->selectionProductsScheduledForDeletion) {
                $this->selectionProductsScheduledForDeletion = clone $this->collSelectionProducts;
                $this->selectionProductsScheduledForDeletion->clear();
            }
            $this->selectionProductsScheduledForDeletion[]= clone $selectionProduct;
            $selectionProduct->setSelection(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Selection is new, it will return
     * an empty collection; or if this Selection has previously
     * been saved, it will retrieve related SelectionProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Selection.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildSelectionProduct[] List of ChildSelectionProduct objects
     */
    public function getSelectionProductsJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSelectionProductQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getSelectionProducts($query, $con);
    }

    /**
     * Clears out the collSelectionContents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSelectionContents()
     */
    public function clearSelectionContents()
    {
        $this->collSelectionContents = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSelectionContents collection loaded partially.
     */
    public function resetPartialSelectionContents($v = true)
    {
        $this->collSelectionContentsPartial = $v;
    }

    /**
     * Initializes the collSelectionContents collection.
     *
     * By default this just sets the collSelectionContents collection to an empty array (like clearcollSelectionContents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSelectionContents($overrideExisting = true)
    {
        if (null !== $this->collSelectionContents && !$overrideExisting) {
            return;
        }
        $this->collSelectionContents = new ObjectCollection();
        $this->collSelectionContents->setModel('\Selection\Model\SelectionContent');
    }

    /**
     * Gets an array of ChildSelectionContent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSelection is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildSelectionContent[] List of ChildSelectionContent objects
     * @throws PropelException
     */
    public function getSelectionContents($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionContentsPartial && !$this->isNew();
        if (null === $this->collSelectionContents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSelectionContents) {
                // return empty collection
                $this->initSelectionContents();
            } else {
                $collSelectionContents = ChildSelectionContentQuery::create(null, $criteria)
                    ->filterBySelection($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSelectionContentsPartial && count($collSelectionContents)) {
                        $this->initSelectionContents(false);

                        foreach ($collSelectionContents as $obj) {
                            if (false == $this->collSelectionContents->contains($obj)) {
                                $this->collSelectionContents->append($obj);
                            }
                        }

                        $this->collSelectionContentsPartial = true;
                    }

                    reset($collSelectionContents);

                    return $collSelectionContents;
                }

                if ($partial && $this->collSelectionContents) {
                    foreach ($this->collSelectionContents as $obj) {
                        if ($obj->isNew()) {
                            $collSelectionContents[] = $obj;
                        }
                    }
                }

                $this->collSelectionContents = $collSelectionContents;
                $this->collSelectionContentsPartial = false;
            }
        }

        return $this->collSelectionContents;
    }

    /**
     * Sets a collection of SelectionContent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $selectionContents A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildSelection The current object (for fluent API support)
     */
    public function setSelectionContents(Collection $selectionContents, ConnectionInterface $con = null)
    {
        $selectionContentsToDelete = $this->getSelectionContents(new Criteria(), $con)->diff($selectionContents);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->selectionContentsScheduledForDeletion = clone $selectionContentsToDelete;

        foreach ($selectionContentsToDelete as $selectionContentRemoved) {
            $selectionContentRemoved->setSelection(null);
        }

        $this->collSelectionContents = null;
        foreach ($selectionContents as $selectionContent) {
            $this->addSelectionContent($selectionContent);
        }

        $this->collSelectionContents = $selectionContents;
        $this->collSelectionContentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SelectionContent objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SelectionContent objects.
     * @throws PropelException
     */
    public function countSelectionContents(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionContentsPartial && !$this->isNew();
        if (null === $this->collSelectionContents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSelectionContents) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSelectionContents());
            }

            $query = ChildSelectionContentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySelection($this)
                ->count($con);
        }

        return count($this->collSelectionContents);
    }

    /**
     * Method called to associate a ChildSelectionContent object to this object
     * through the ChildSelectionContent foreign key attribute.
     *
     * @param    ChildSelectionContent $l ChildSelectionContent
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function addSelectionContent(ChildSelectionContent $l)
    {
        if ($this->collSelectionContents === null) {
            $this->initSelectionContents();
            $this->collSelectionContentsPartial = true;
        }

        if (!in_array($l, $this->collSelectionContents->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSelectionContent($l);
        }

        return $this;
    }

    /**
     * @param SelectionContent $selectionContent The selectionContent object to add.
     */
    protected function doAddSelectionContent($selectionContent)
    {
        $this->collSelectionContents[]= $selectionContent;
        $selectionContent->setSelection($this);
    }

    /**
     * @param  SelectionContent $selectionContent The selectionContent object to remove.
     * @return ChildSelection The current object (for fluent API support)
     */
    public function removeSelectionContent($selectionContent)
    {
        if ($this->getSelectionContents()->contains($selectionContent)) {
            $this->collSelectionContents->remove($this->collSelectionContents->search($selectionContent));
            if (null === $this->selectionContentsScheduledForDeletion) {
                $this->selectionContentsScheduledForDeletion = clone $this->collSelectionContents;
                $this->selectionContentsScheduledForDeletion->clear();
            }
            $this->selectionContentsScheduledForDeletion[]= clone $selectionContent;
            $selectionContent->setSelection(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Selection is new, it will return
     * an empty collection; or if this Selection has previously
     * been saved, it will retrieve related SelectionContents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Selection.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildSelectionContent[] List of ChildSelectionContent objects
     */
    public function getSelectionContentsJoinContent($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSelectionContentQuery::create(null, $criteria);
        $query->joinWith('Content', $joinBehavior);

        return $this->getSelectionContents($query, $con);
    }

    /**
     * Clears out the collSelectionImages collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSelectionImages()
     */
    public function clearSelectionImages()
    {
        $this->collSelectionImages = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSelectionImages collection loaded partially.
     */
    public function resetPartialSelectionImages($v = true)
    {
        $this->collSelectionImagesPartial = $v;
    }

    /**
     * Initializes the collSelectionImages collection.
     *
     * By default this just sets the collSelectionImages collection to an empty array (like clearcollSelectionImages());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSelectionImages($overrideExisting = true)
    {
        if (null !== $this->collSelectionImages && !$overrideExisting) {
            return;
        }
        $this->collSelectionImages = new ObjectCollection();
        $this->collSelectionImages->setModel('\Selection\Model\SelectionImage');
    }

    /**
     * Gets an array of ChildSelectionImage objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSelection is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildSelectionImage[] List of ChildSelectionImage objects
     * @throws PropelException
     */
    public function getSelectionImages($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionImagesPartial && !$this->isNew();
        if (null === $this->collSelectionImages || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSelectionImages) {
                // return empty collection
                $this->initSelectionImages();
            } else {
                $collSelectionImages = ChildSelectionImageQuery::create(null, $criteria)
                    ->filterBySelection($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSelectionImagesPartial && count($collSelectionImages)) {
                        $this->initSelectionImages(false);

                        foreach ($collSelectionImages as $obj) {
                            if (false == $this->collSelectionImages->contains($obj)) {
                                $this->collSelectionImages->append($obj);
                            }
                        }

                        $this->collSelectionImagesPartial = true;
                    }

                    reset($collSelectionImages);

                    return $collSelectionImages;
                }

                if ($partial && $this->collSelectionImages) {
                    foreach ($this->collSelectionImages as $obj) {
                        if ($obj->isNew()) {
                            $collSelectionImages[] = $obj;
                        }
                    }
                }

                $this->collSelectionImages = $collSelectionImages;
                $this->collSelectionImagesPartial = false;
            }
        }

        return $this->collSelectionImages;
    }

    /**
     * Sets a collection of SelectionImage objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $selectionImages A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildSelection The current object (for fluent API support)
     */
    public function setSelectionImages(Collection $selectionImages, ConnectionInterface $con = null)
    {
        $selectionImagesToDelete = $this->getSelectionImages(new Criteria(), $con)->diff($selectionImages);


        $this->selectionImagesScheduledForDeletion = $selectionImagesToDelete;

        foreach ($selectionImagesToDelete as $selectionImageRemoved) {
            $selectionImageRemoved->setSelection(null);
        }

        $this->collSelectionImages = null;
        foreach ($selectionImages as $selectionImage) {
            $this->addSelectionImage($selectionImage);
        }

        $this->collSelectionImages = $selectionImages;
        $this->collSelectionImagesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SelectionImage objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SelectionImage objects.
     * @throws PropelException
     */
    public function countSelectionImages(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionImagesPartial && !$this->isNew();
        if (null === $this->collSelectionImages || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSelectionImages) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSelectionImages());
            }

            $query = ChildSelectionImageQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySelection($this)
                ->count($con);
        }

        return count($this->collSelectionImages);
    }

    /**
     * Method called to associate a ChildSelectionImage object to this object
     * through the ChildSelectionImage foreign key attribute.
     *
     * @param    ChildSelectionImage $l ChildSelectionImage
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function addSelectionImage(ChildSelectionImage $l)
    {
        if ($this->collSelectionImages === null) {
            $this->initSelectionImages();
            $this->collSelectionImagesPartial = true;
        }

        if (!in_array($l, $this->collSelectionImages->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSelectionImage($l);
        }

        return $this;
    }

    /**
     * @param SelectionImage $selectionImage The selectionImage object to add.
     */
    protected function doAddSelectionImage($selectionImage)
    {
        $this->collSelectionImages[]= $selectionImage;
        $selectionImage->setSelection($this);
    }

    /**
     * @param  SelectionImage $selectionImage The selectionImage object to remove.
     * @return ChildSelection The current object (for fluent API support)
     */
    public function removeSelectionImage($selectionImage)
    {
        if ($this->getSelectionImages()->contains($selectionImage)) {
            $this->collSelectionImages->remove($this->collSelectionImages->search($selectionImage));
            if (null === $this->selectionImagesScheduledForDeletion) {
                $this->selectionImagesScheduledForDeletion = clone $this->collSelectionImages;
                $this->selectionImagesScheduledForDeletion->clear();
            }
            $this->selectionImagesScheduledForDeletion[]= clone $selectionImage;
            $selectionImage->setSelection(null);
        }

        return $this;
    }

    /**
     * Clears out the collSelectionI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSelectionI18ns()
     */
    public function clearSelectionI18ns()
    {
        $this->collSelectionI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSelectionI18ns collection loaded partially.
     */
    public function resetPartialSelectionI18ns($v = true)
    {
        $this->collSelectionI18nsPartial = $v;
    }

    /**
     * Initializes the collSelectionI18ns collection.
     *
     * By default this just sets the collSelectionI18ns collection to an empty array (like clearcollSelectionI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSelectionI18ns($overrideExisting = true)
    {
        if (null !== $this->collSelectionI18ns && !$overrideExisting) {
            return;
        }
        $this->collSelectionI18ns = new ObjectCollection();
        $this->collSelectionI18ns->setModel('\Selection\Model\SelectionI18n');
    }

    /**
     * Gets an array of ChildSelectionI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSelection is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildSelectionI18n[] List of ChildSelectionI18n objects
     * @throws PropelException
     */
    public function getSelectionI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionI18nsPartial && !$this->isNew();
        if (null === $this->collSelectionI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSelectionI18ns) {
                // return empty collection
                $this->initSelectionI18ns();
            } else {
                $collSelectionI18ns = ChildSelectionI18nQuery::create(null, $criteria)
                    ->filterBySelection($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSelectionI18nsPartial && count($collSelectionI18ns)) {
                        $this->initSelectionI18ns(false);

                        foreach ($collSelectionI18ns as $obj) {
                            if (false == $this->collSelectionI18ns->contains($obj)) {
                                $this->collSelectionI18ns->append($obj);
                            }
                        }

                        $this->collSelectionI18nsPartial = true;
                    }

                    reset($collSelectionI18ns);

                    return $collSelectionI18ns;
                }

                if ($partial && $this->collSelectionI18ns) {
                    foreach ($this->collSelectionI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collSelectionI18ns[] = $obj;
                        }
                    }
                }

                $this->collSelectionI18ns = $collSelectionI18ns;
                $this->collSelectionI18nsPartial = false;
            }
        }

        return $this->collSelectionI18ns;
    }

    /**
     * Sets a collection of SelectionI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $selectionI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildSelection The current object (for fluent API support)
     */
    public function setSelectionI18ns(Collection $selectionI18ns, ConnectionInterface $con = null)
    {
        $selectionI18nsToDelete = $this->getSelectionI18ns(new Criteria(), $con)->diff($selectionI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->selectionI18nsScheduledForDeletion = clone $selectionI18nsToDelete;

        foreach ($selectionI18nsToDelete as $selectionI18nRemoved) {
            $selectionI18nRemoved->setSelection(null);
        }

        $this->collSelectionI18ns = null;
        foreach ($selectionI18ns as $selectionI18n) {
            $this->addSelectionI18n($selectionI18n);
        }

        $this->collSelectionI18ns = $selectionI18ns;
        $this->collSelectionI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SelectionI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SelectionI18n objects.
     * @throws PropelException
     */
    public function countSelectionI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSelectionI18nsPartial && !$this->isNew();
        if (null === $this->collSelectionI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSelectionI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSelectionI18ns());
            }

            $query = ChildSelectionI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySelection($this)
                ->count($con);
        }

        return count($this->collSelectionI18ns);
    }

    /**
     * Method called to associate a ChildSelectionI18n object to this object
     * through the ChildSelectionI18n foreign key attribute.
     *
     * @param    ChildSelectionI18n $l ChildSelectionI18n
     * @return   \Selection\Model\Selection The current object (for fluent API support)
     */
    public function addSelectionI18n(ChildSelectionI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collSelectionI18ns === null) {
            $this->initSelectionI18ns();
            $this->collSelectionI18nsPartial = true;
        }

        if (!in_array($l, $this->collSelectionI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSelectionI18n($l);
        }

        return $this;
    }

    /**
     * @param SelectionI18n $selectionI18n The selectionI18n object to add.
     */
    protected function doAddSelectionI18n($selectionI18n)
    {
        $this->collSelectionI18ns[]= $selectionI18n;
        $selectionI18n->setSelection($this);
    }

    /**
     * @param  SelectionI18n $selectionI18n The selectionI18n object to remove.
     * @return ChildSelection The current object (for fluent API support)
     */
    public function removeSelectionI18n($selectionI18n)
    {
        if ($this->getSelectionI18ns()->contains($selectionI18n)) {
            $this->collSelectionI18ns->remove($this->collSelectionI18ns->search($selectionI18n));
            if (null === $this->selectionI18nsScheduledForDeletion) {
                $this->selectionI18nsScheduledForDeletion = clone $this->collSelectionI18ns;
                $this->selectionI18nsScheduledForDeletion->clear();
            }
            $this->selectionI18nsScheduledForDeletion[]= clone $selectionI18n;
            $selectionI18n->setSelection(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->visible = null;
        $this->position = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collSelectionProducts) {
                foreach ($this->collSelectionProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSelectionContents) {
                foreach ($this->collSelectionContents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSelectionImages) {
                foreach ($this->collSelectionImages as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSelectionI18ns) {
                foreach ($this->collSelectionI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collSelectionProducts = null;
        $this->collSelectionContents = null;
        $this->collSelectionImages = null;
        $this->collSelectionI18ns = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SelectionTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildSelection The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[SelectionTableMap::UPDATED_AT] = true;

        return $this;
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildSelection The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildSelectionI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collSelectionI18ns) {
                foreach ($this->collSelectionI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildSelectionI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildSelectionI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addSelectionI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildSelection The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildSelectionI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collSelectionI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collSelectionI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildSelectionI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [title] column value.
         *
         * @return   string
         */
        public function getTitle()
        {
        return $this->getCurrentTranslation()->getTitle();
    }


        /**
         * Set the value of [title] column.
         *
         * @param      string $v new value
         * @return   \Selection\Model\SelectionI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

        return $this;
    }


        /**
         * Get the [description] column value.
         *
         * @return   string
         */
        public function getDescription()
        {
        return $this->getCurrentTranslation()->getDescription();
    }


        /**
         * Set the value of [description] column.
         *
         * @param      string $v new value
         * @return   \Selection\Model\SelectionI18n The current object (for fluent API support)
         */
        public function setDescription($v)
        {    $this->getCurrentTranslation()->setDescription($v);

        return $this;
    }


        /**
         * Get the [chapo] column value.
         *
         * @return   string
         */
        public function getChapo()
        {
        return $this->getCurrentTranslation()->getChapo();
    }


        /**
         * Set the value of [chapo] column.
         *
         * @param      string $v new value
         * @return   \Selection\Model\SelectionI18n The current object (for fluent API support)
         */
        public function setChapo($v)
        {    $this->getCurrentTranslation()->setChapo($v);

        return $this;
    }


        /**
         * Get the [postscriptum] column value.
         *
         * @return   string
         */
        public function getPostscriptum()
        {
        return $this->getCurrentTranslation()->getPostscriptum();
    }


        /**
         * Set the value of [postscriptum] column.
         *
         * @param      string $v new value
         * @return   \Selection\Model\SelectionI18n The current object (for fluent API support)
         */
        public function setPostscriptum($v)
        {    $this->getCurrentTranslation()->setPostscriptum($v);

        return $this;
    }


        /**
         * Get the [meta_title] column value.
         *
         * @return   string
         */
        public function getMetaTitle()
        {
        return $this->getCurrentTranslation()->getMetaTitle();
    }


        /**
         * Set the value of [meta_title] column.
         *
         * @param      string $v new value
         * @return   \Selection\Model\SelectionI18n The current object (for fluent API support)
         */
        public function setMetaTitle($v)
        {    $this->getCurrentTranslation()->setMetaTitle($v);

        return $this;
    }


        /**
         * Get the [meta_description] column value.
         *
         * @return   string
         */
        public function getMetaDescription()
        {
        return $this->getCurrentTranslation()->getMetaDescription();
    }


        /**
         * Set the value of [meta_description] column.
         *
         * @param      string $v new value
         * @return   \Selection\Model\SelectionI18n The current object (for fluent API support)
         */
        public function setMetaDescription($v)
        {    $this->getCurrentTranslation()->setMetaDescription($v);

        return $this;
    }


        /**
         * Get the [meta_keywords] column value.
         *
         * @return   string
         */
        public function getMetaKeywords()
        {
        return $this->getCurrentTranslation()->getMetaKeywords();
    }


        /**
         * Set the value of [meta_keywords] column.
         *
         * @param      string $v new value
         * @return   \Selection\Model\SelectionI18n The current object (for fluent API support)
         */
        public function setMetaKeywords($v)
        {    $this->getCurrentTranslation()->setMetaKeywords($v);

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
