<?php

require_once(LIB_PATH.DS.'database.php');

abstract class GeoProcessor
{

    // abstract protected static function geom_to_geojson($geom);

    // abstract protected static function geom_to_text($geom);

    /**
     * dd_to_dms
     * converts a point from decimal degrees to degress minutes seconds
     * @param string $latitude
     * @param string $longitude
     * @return object
     */
    public static function dd_to_dms($wkt)
    {
        global $db;

        $sql = "SELECT (ST_AsLatLonText(?)) As dms";
        $options = array($wkt);

        $sth = $db->query($sql, $options)->fetch();

        return $sth;
    }

    /**
     * dd_to_ddm
     * converts a point from decimal degrees to degress decimal minutes
     * @param string $latitude
     * @param string $longitude
     * @return object
     */
    public static function dd_to_ddm($wkt)
    {
        global $db;

        $sql = "SELECT (ST_AsLatLonText(?, 'D°MM.MMMC')) As dms";
        $options = array($wkt);

        $sth = $db->query($sql, $options)->fetch();

        return $sth;
    }

    /**
     * dms_to_dd
     * converts degrees minutes seconds or degrees decimal minutes to decimal degrees (custom function)
     * @param string $latitude
     * @param string $longitude
     * @return object
     */
    public static function dms_to_dd($latitude, $longitude)
    {
        global $db;

        $sql = "SELECT round(dms2dd(?),9) as latitude, round(dms2dd(?),9) as longitude;";
        $options = array($latitude, $longitude);

        $sth = $db->query($sql, $options)->fetch();

        return $sth;
    }

    /**
     * calculate_azimuth
     * calculates the heading(azimuth), given two points
     * @param string $wkt1
     * @param string $wkt2
     * @return object
     */
    public static function calculate_azimuth($wkt1, $wkt2)
    {
        global $db;

        $sql = "SELECT degrees(ST_Azimuth(?::geometry::geography,?::geometry::geography)) AS dega_b";
        $options = array($wkt1, $wkt2);

        $sth = $db->query($sql, $options);
        
        return $sth->fetch();
    }

    /**
     * geog_to_linestring
     * converts geography type object to text format
     * @param geometry pbject $geog
     * @return object with text representation
     */
    public static function geog_to_linestring($geog)
    {
        global $db;

        $sql = "SELECT ST_AsText(?) AS text_line";
        $options = array($geog);

        $sth = $db->query($sql, $options);
        
        return array_shift($sth->fetch());
    }

    /**
     * calculate_distance, calculate distance between two geometry objects.
     * static function for calculating spherical distance bettwen two given objects
     * @param string $wkt1
     * @param string $wkt2
     * @return associative array with key distance_geog and value the distance calculated
     */
    public static function calculate_distance($wkt1, $wkt2)
    {
        global $db;

        $sql = "SELECT ST_Distance(?::geometry::geography, ?::geometry::geography) As distance_geog";
        $options = array($wkt1, $wkt2);

        $sth = $db->query($sql, $options);
        
        return $sth->fetch();
    }
}
