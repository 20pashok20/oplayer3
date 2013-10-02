<?php

namespace Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Model\Playlist;
use Model\PlaylistTrack;
use Model\PlaylistTrackPeer;
use Model\PlaylistTrackQuery;

/**
 * Base class that represents a query for the 'playlist_track' table.
 *
 *
 *
 * @method PlaylistTrackQuery orderById($order = Criteria::ASC) Order by the id column
 * @method PlaylistTrackQuery orderByPlaylistid($order = Criteria::ASC) Order by the playlistId column
 * @method PlaylistTrackQuery orderByTrack($order = Criteria::ASC) Order by the track column
 *
 * @method PlaylistTrackQuery groupById() Group by the id column
 * @method PlaylistTrackQuery groupByPlaylistid() Group by the playlistId column
 * @method PlaylistTrackQuery groupByTrack() Group by the track column
 *
 * @method PlaylistTrackQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PlaylistTrackQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PlaylistTrackQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PlaylistTrackQuery leftJoinPlaylist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Playlist relation
 * @method PlaylistTrackQuery rightJoinPlaylist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Playlist relation
 * @method PlaylistTrackQuery innerJoinPlaylist($relationAlias = null) Adds a INNER JOIN clause to the query using the Playlist relation
 *
 * @method PlaylistTrack findOne(PropelPDO $con = null) Return the first PlaylistTrack matching the query
 * @method PlaylistTrack findOneOrCreate(PropelPDO $con = null) Return the first PlaylistTrack matching the query, or a new PlaylistTrack object populated from the query conditions when no match is found
 *
 * @method PlaylistTrack findOneByPlaylistid(int $playlistId) Return the first PlaylistTrack filtered by the playlistId column
 * @method PlaylistTrack findOneByTrack(string $track) Return the first PlaylistTrack filtered by the track column
 *
 * @method array findById(int $id) Return PlaylistTrack objects filtered by the id column
 * @method array findByPlaylistid(int $playlistId) Return PlaylistTrack objects filtered by the playlistId column
 * @method array findByTrack(string $track) Return PlaylistTrack objects filtered by the track column
 *
 * @package    propel.generator.Model.om
 */
abstract class BasePlaylistTrackQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePlaylistTrackQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'artcms', $modelName = 'Model\\PlaylistTrack', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PlaylistTrackQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PlaylistTrackQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlaylistTrackQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlaylistTrackQuery) {
            return $criteria;
        }
        $query = new PlaylistTrackQuery();
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
     * @param     PropelPDO $con an optional connection object
     *
     * @return   PlaylistTrack|PlaylistTrack[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PlaylistTrackPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PlaylistTrackPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 PlaylistTrack A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 PlaylistTrack A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `playlistId`, `track` FROM `playlist_track` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new PlaylistTrack();
            $obj->hydrate($row);
            PlaylistTrackPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return PlaylistTrack|PlaylistTrack[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|PlaylistTrack[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return PlaylistTrackQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PlaylistTrackPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PlaylistTrackQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PlaylistTrackPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlaylistTrackQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PlaylistTrackPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PlaylistTrackPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlaylistTrackPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the playlistId column
     *
     * Example usage:
     * <code>
     * $query->filterByPlaylistid(1234); // WHERE playlistId = 1234
     * $query->filterByPlaylistid(array(12, 34)); // WHERE playlistId IN (12, 34)
     * $query->filterByPlaylistid(array('min' => 12)); // WHERE playlistId >= 12
     * $query->filterByPlaylistid(array('max' => 12)); // WHERE playlistId <= 12
     * </code>
     *
     * @see       filterByPlaylist()
     *
     * @param     mixed $playlistid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlaylistTrackQuery The current query, for fluid interface
     */
    public function filterByPlaylistid($playlistid = null, $comparison = null)
    {
        if (is_array($playlistid)) {
            $useMinMax = false;
            if (isset($playlistid['min'])) {
                $this->addUsingAlias(PlaylistTrackPeer::PLAYLISTID, $playlistid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playlistid['max'])) {
                $this->addUsingAlias(PlaylistTrackPeer::PLAYLISTID, $playlistid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlaylistTrackPeer::PLAYLISTID, $playlistid, $comparison);
    }

    /**
     * Filter the query on the track column
     *
     * Example usage:
     * <code>
     * $query->filterByTrack('fooValue');   // WHERE track = 'fooValue'
     * $query->filterByTrack('%fooValue%'); // WHERE track LIKE '%fooValue%'
     * </code>
     *
     * @param     string $track The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlaylistTrackQuery The current query, for fluid interface
     */
    public function filterByTrack($track = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($track)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $track)) {
                $track = str_replace('*', '%', $track);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlaylistTrackPeer::TRACK, $track, $comparison);
    }

    /**
     * Filter the query by a related Playlist object
     *
     * @param   Playlist|PropelObjectCollection $playlist The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlaylistTrackQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlaylist($playlist, $comparison = null)
    {
        if ($playlist instanceof Playlist) {
            return $this
                ->addUsingAlias(PlaylistTrackPeer::PLAYLISTID, $playlist->getId(), $comparison);
        } elseif ($playlist instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PlaylistTrackPeer::PLAYLISTID, $playlist->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPlaylist() only accepts arguments of type Playlist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Playlist relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlaylistTrackQuery The current query, for fluid interface
     */
    public function joinPlaylist($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Playlist');

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
            $this->addJoinObject($join, 'Playlist');
        }

        return $this;
    }

    /**
     * Use the Playlist relation Playlist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Model\PlaylistQuery A secondary query class using the current class as primary query
     */
    public function usePlaylistQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlaylist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Playlist', '\Model\PlaylistQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   PlaylistTrack $playlistTrack Object to remove from the list of results
     *
     * @return PlaylistTrackQuery The current query, for fluid interface
     */
    public function prune($playlistTrack = null)
    {
        if ($playlistTrack) {
            $this->addUsingAlias(PlaylistTrackPeer::ID, $playlistTrack->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
