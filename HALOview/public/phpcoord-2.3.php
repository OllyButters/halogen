<?php 
//////////////////////////////////////////////////////////////////////
// HALO-view; a web-based geospatial visualisation tool.
// Written by Olly Butters (<http://www.faji.co.uk>).
// Copyright (C) 2011 University of Leicester (<http://www.le.ac.uk/legal>)
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//////////////////////////////////////////////////////////////////////

?>
<?php
//This file is derived from phpcoord.php (c) 2005 Jonathan Stott
//Itself released under a GNU General Public License.

  class LatLng {

    var $lat;
    var $lng;


    /**
     * Create a new LatLng object from the given latitude and longitude
     *
     * @param lat latitude
     * @param lng longitude
     */
    function LatLng($lat, $lng) {
      $this->lat = $lat;
      $this->lng = $lng;
    }


    /**
     * Return a string representation of this LatLng object
     *
     * @return a string representation of this LatLng object
     */
    function toString() {
      return "(" . $this->lat . ", " . $this->lng . ")";
    }
    
    function toStringGoogle() {
      return $this->lng . ", " . $this->lat;
    }

    function toStringLat()
    {
      return $this->lat;
    }
    
    function toStringLong()
    {
      return $this->lng;
    }

    /**
     * Calculate the surface distance between this LatLng object and the one
     * passed in as a parameter.
     *
     * @param to a LatLng object to measure the surface distance to
     * @return the surface distance
     */
    function distance($to) {
      $er = 6366.707;

      $latFrom = deg2rad($this->lat);
      $latTo   = deg2rad($to->lat);
      $lngFrom = deg2rad($this->lng);
      $lngTo   = deg2rad($to->lng);

      $x1 = $er * cos($lngFrom) * sin($latFrom);
      $y1 = $er * sin($lngFrom) * sin($latFrom);
      $z1 = $er * cos($latFrom);

      $x2 = $er * cos($lngTo) * sin($latTo);
      $y2 = $er * sin($lngTo) * sin($latTo);
      $z2 = $er * cos($latTo);

      $d = acos(sin($latFrom)*sin($latTo) + cos($latFrom)*cos($latTo)*cos($lngTo-$lngFrom)) * $er;
      
      return $d;
    }

    
    /**
     * Convert this LatLng object from OSGB36 datum to WGS84 datum.
     */
    function OSGB36ToWGS84() {
      $airy1830 = new RefEll(6377563.396, 6356256.909);
      $a        = $airy1830->maj;
      $b        = $airy1830->min;
      $eSquared = $airy1830->ecc;
      $phi = deg2rad($this->lat);
      $lambda = deg2rad($this->lng);
      $v = $a / (sqrt(1 - $eSquared * sinSquared($phi)));
      $H = 0; // height
      $x = ($v + $H) * cos($phi) * cos($lambda);
      $y = ($v + $H) * cos($phi) * sin($lambda);
      $z = ((1 - $eSquared) * $v + $H) * sin($phi);

      $tx =        446.448;
      $ty =       -124.157;
      $tz =        542.060;
      $s  =         -0.0000204894;
      $rx = deg2rad( 0.00004172222);
      $ry = deg2rad( 0.00006861111);
      $rz = deg2rad( 0.00023391666);

      $xB = $tx + ($x * (1 + $s)) + (-$rx * $y)     + ($ry * $z);
      $yB = $ty + ($rz * $x)      + ($y * (1 + $s)) + (-$rx * $z);
      $zB = $tz + (-$ry * $x)     + ($rx * $y)      + ($z * (1 + $s));

      $wgs84 = new RefEll(6378137.000, 6356752.3141);
      $a        = $wgs84->maj;
      $b        = $wgs84->min;
      $eSquared = $wgs84->ecc;

      $lambdaB = rad2deg(atan($yB / $xB));
      $p = sqrt(($xB * $xB) + ($yB * $yB));
      $phiN = atan($zB / ($p * (1 - $eSquared)));
      for ($i = 1; $i < 10; $i++) {
        $v = $a / (sqrt(1 - $eSquared * sinSquared($phiN)));
        $phiN1 = atan(($zB + ($eSquared * $v * sin($phiN))) / $p);
        $phiN = $phiN1;
      }

      $phiB = rad2deg($phiN);
      
      $this->lat = $phiB;
      $this->lng = $lambdaB;
    }
    
    
    /**
     * Convert this LatLng object from WGS84 datum to OSGB36 datum.
     */
    function WGS84ToOSGB36() {
      $wgs84 = new RefEll(6378137.000, 6356752.3141);
      $a        = $wgs84->maj;
      $b        = $wgs84->min;
      $eSquared = $wgs84->ecc;
      $phi = deg2rad($this->lat);
      $lambda = deg2rad($this->lng);
      $v = $a / (sqrt(1 - $eSquared * sinSquared($phi)));
      $H = 0; // height
      $x = ($v + $H) * cos($phi) * cos($lambda);
      $y = ($v + $H) * cos($phi) * sin($lambda);
      $z = ((1 - $eSquared) * $v + $H) * sin($phi);

      $tx =       -446.448;
      $ty =        124.157;
      $tz =       -542.060;
      $s  =          0.0000204894;
      $rx = deg2rad(-0.00004172222);
      $ry = deg2rad(-0.00006861111);
      $rz = deg2rad(-0.00023391666);

      $xB = $tx + ($x * (1 + $s)) + (-$rx * $y)     + ($ry * $z);
      $yB = $ty + ($rz * $x)      + ($y * (1 + $s)) + (-$rx * $z);
      $zB = $tz + (-$ry * $x)     + ($rx * $y)      + ($z * (1 + $s));

      $airy1830 = new RefEll(6377563.396, 6356256.909);
      $a        = $airy1830->maj;
      $b        = $airy1830->min;
      $eSquared = $airy1830->ecc;

      $lambdaB = rad2deg(atan($yB / $xB));
      $p = sqrt(($xB * $xB) + ($yB * $yB));
      $phiN = atan($zB / ($p * (1 - $eSquared)));
      for ($i = 1; $i < 10; $i++) {
        $v = $a / (sqrt(1 - $eSquared * sinSquared($phiN)));
        $phiN1 = atan(($zB + ($eSquared * $v * sin($phiN))) / $p);
        $phiN = $phiN1;
      }
 
      $phiB = rad2deg($phiN);
      
      $this->lat = $phiB;
      $this->lng = $lambdaB;
    }
    
    
    /**
     * Convert this LatLng object into an OSGB grid reference. Note that this
     * function does not take into account the bounds of the OSGB grid -
     * beyond the bounds of the OSGB grid, the resulting OSRef object has no
     * meaning
     *
     * @return the converted OSGB grid reference
     */
    function toOSRef() {
      $airy1830 = new RefEll(6377563.396, 6356256.909);
      $OSGB_F0  = 0.9996012717;
      $N0       = -100000.0;
      $E0       = 400000.0;
      $phi0     = deg2rad(49.0);
      $lambda0  = deg2rad(-2.0);
      $a        = $airy1830->maj;
      $b        = $airy1830->min;
      $eSquared = $airy1830->ecc;
      $phi = deg2rad($this->lat);
      $lambda = deg2rad($this->lng);
      $E = 0.0;
      $N = 0.0;
      $n = ($a - $b) / ($a + $b);
      $v = $a * $OSGB_F0 * pow(1.0 - $eSquared * sinSquared($phi), -0.5);
      $rho =
        $a * $OSGB_F0 * (1.0 - $eSquared) * pow(1.0 - $eSquared * sinSquared($phi), -1.5);
      $etaSquared = ($v / $rho) - 1.0;
      $M =
        ($b * $OSGB_F0)
          * (((1 + $n + ((5.0 / 4.0) * $n * $n) + ((5.0 / 4.0) * $n * $n * $n))
            * ($phi - $phi0))
            - (((3 * $n) + (3 * $n * $n) + ((21.0 / 8.0) * $n * $n * $n))
              * sin($phi - $phi0)
              * cos($phi + $phi0))
            + ((((15.0 / 8.0) * $n * $n) + ((15.0 / 8.0) * $n * $n * $n))
              * sin(2.0 * ($phi - $phi0))
              * cos(2.0 * ($phi + $phi0)))
            - (((35.0 / 24.0) * $n * $n * $n)
              * sin(3.0 * ($phi - $phi0))
              * cos(3.0 * ($phi + $phi0))));
      $I = $M + $N0;
      $II = ($v / 2.0) * sin($phi) * cos($phi);
      $III =
        ($v / 24.0)
          * sin($phi)
          * pow(cos($phi), 3.0)
          * (5.0 - tanSquared($phi) + (9.0 * $etaSquared));
      $IIIA =
        ($v / 720.0)
          * sin($phi)
          * pow(cos($phi), 5.0)
          * (61.0 - (58.0 * tanSquared($phi)) + pow(tan($phi), 4.0));
      $IV = $v * cos($phi);
      $V = ($v / 6.0) * pow(cos($phi), 3.0) * (($v / $rho) - tanSquared($phi));
      $VI =
        ($v / 120.0)
          * pow(cos($phi), 5.0)
          * (5.0
            - (18.0 * tanSquared($phi))
            + (pow(tan($phi), 4.0))
            + (14 * $etaSquared)
            - (58 * tanSquared($phi) * $etaSquared));

      $N =
        $I
          + ($II * pow($lambda - $lambda0, 2.0))
          + ($III * pow($lambda - $lambda0, 4.0))
          + ($IIIA * pow($lambda - $lambda0, 6.0));
      $E =
        $E0
          + ($IV * ($lambda - $lambda0))
          + ($V * pow($lambda - $lambda0, 3.0))
          + ($VI * pow($lambda - $lambda0, 5.0));

      return new OSRef($E, $N);
    }
  }    
    



  // =================================================================== OSRef

  // References given with OSRef are accurate to 1m.
  class OSRef {

    var $easting;
    var $northing;


    /**
     * Create a new OSRef object representing an OSGB grid reference. Note
     * that the parameters for this constructor require eastings and
     * northings with 1m accuracy and need to be absolute with respect to
     * the whole of the British Grid. For example, to create an OSRef
     * object from the six-figure grid reference TG514131, the easting would
     * be 651400 and the northing would be 313100.
     * 
     * Grid references with accuracy greater than 1m can be represented
     * using floating point values for the easting and northing. For example,
     * a value representing an easting or northing accurate to 1mm would be
     * given as 651400.0001.
     *
     * @param easting the easting of the reference (with 1m accuracy)
     * @param northing the northing of the reference (with 1m accuracy)
     */
    function OSRef($easting, $northing) {
      $this->easting  = $easting;
      $this->northing = $northing;
    }


    /**
     * Convert this grid reference into a string showing the exact values
     * of the easting and northing.
     *
     * @return
     */
    function toString() {
      return "(" . $this->easting . ", " . $this->northing . ")";
    }


    function toStringEasting() {
      return $this->easting;
    }

    function toStringNorthing() {
      return $this->northing;
    }

    /**
     * Convert this grid reference into a string using a standard six-figure
     * grid reference including the two-character designation for the 100km
     * square. e.g. TG514131. 
     *
     * @return
     */
    function toSixFigureString() {
      $hundredkmE = floor($this->easting / 100000);
      $hundredkmN = floor($this->northing / 100000);
      $firstLetter = "";
      if ($hundredkmN < 5) {
        if ($hundredkmE < 5) {
          $firstLetter = "S";
        } else {
          $firstLetter = "T";
        }
      } else if ($hundredkmN < 10) {
        if ($hundredkmE < 5) {
          $firstLetter = "N";
        } else {
          $firstLetter = "O";
        }
      } else {
        $firstLetter = "H";
      }

      $secondLetter = "";
      $index = 65 + ((4 - ($hundredkmN % 5)) * 5) + ($hundredkmE % 5);
      $ti = $index;
      if ($index >= 73) $index++;
      $secondLetter = chr($index);

      $e = round(($this->easting - (100000 * $hundredkmE)) / 100);
      $n = round(($this->northing - (100000 * $hundredkmN)) / 100);

      return sprintf("%s%s%03d%03d", $firstLetter, $secondLetter, $e, $n);
    }


    /**
     * Convert this grid reference into a latitude and longitude
     *
     * @return
     */
    function toLatLng() {
      $airy1830 = new RefEll(6377563.396, 6356256.909);
      $OSGB_F0  = 0.9996012717;
      $N0       = -100000.0;
      $E0       = 400000.0;
      $phi0     = deg2rad(49.0);
      $lambda0  = deg2rad(-2.0);
      $a        = $airy1830->maj;
      $b        = $airy1830->min;
      $eSquared = $airy1830->ecc;
      $phi      = 0.0;
      $lambda   = 0.0;
      $E        = $this->easting;
      $N        = $this->northing;
      $n        = ($a - $b) / ($a + $b);
      $M        = 0.0;
      $phiPrime = (($N - $N0) / ($a * $OSGB_F0)) + $phi0;
      do {
        $M =
          ($b * $OSGB_F0)
            * (((1 + $n + ((5.0 / 4.0) * $n * $n) + ((5.0 / 4.0) * $n * $n * $n))
              * ($phiPrime - $phi0))
              - (((3 * $n) + (3 * $n * $n) + ((21.0 / 8.0) * $n * $n * $n))
                * sin($phiPrime - $phi0)
                * cos($phiPrime + $phi0))
              + ((((15.0 / 8.0) * $n * $n) + ((15.0 / 8.0) * $n * $n * $n))
                * sin(2.0 * ($phiPrime - $phi0))
                * cos(2.0 * ($phiPrime + $phi0)))
              - (((35.0 / 24.0) * $n * $n * $n)
                * sin(3.0 * ($phiPrime - $phi0))
                * cos(3.0 * ($phiPrime + $phi0))));
        $phiPrime += ($N - $N0 - $M) / ($a * $OSGB_F0);
      } while (($N - $N0 - $M) >= 0.001);
      $v = $a * $OSGB_F0 * pow(1.0 - $eSquared * sinSquared($phiPrime), -0.5);
      $rho =
        $a
          * $OSGB_F0
          * (1.0 - $eSquared)
          * pow(1.0 - $eSquared * sinSquared($phiPrime), -1.5);
      $etaSquared = ($v / $rho) - 1.0;
      $VII = tan($phiPrime) / (2 * $rho * $v);
      $VIII =
        (tan($phiPrime) / (24.0 * $rho * pow($v, 3.0)))
          * (5.0
            + (3.0 * tanSquared($phiPrime))
            + $etaSquared
            - (9.0 * tanSquared($phiPrime) * $etaSquared));
      $IX =
        (tan($phiPrime) / (720.0 * $rho * pow($v, 5.0)))
          * (61.0
            + (90.0 * tanSquared($phiPrime))
            + (45.0 * tanSquared($phiPrime) * tanSquared($phiPrime)));
      $X = sec($phiPrime) / $v;
      $XI =
        (sec($phiPrime) / (6.0 * $v * $v * $v))
          * (($v / $rho) + (2 * tanSquared($phiPrime)));
      $XII =
        (sec($phiPrime) / (120.0 * pow($v, 5.0)))
          * (5.0
            + (28.0 * tanSquared($phiPrime))
            + (24.0 * tanSquared($phiPrime) * tanSquared($phiPrime)));
      $XIIA =
        (sec($phiPrime) / (5040.0 * pow($v, 7.0)))
          * (61.0
            + (662.0 * tanSquared($phiPrime))
            + (1320.0 * tanSquared($phiPrime) * tanSquared($phiPrime))
            + (720.0
              * tanSquared($phiPrime)
              * tanSquared($phiPrime)
              * tanSquared($phiPrime)));
      $phi =
        $phiPrime
          - ($VII * pow($E - $E0, 2.0))
          + ($VIII * pow($E - $E0, 4.0))
          - ($IX * pow($E - $E0, 6.0));
      $lambda =
        $lambda0
          + ($X * ($E - $E0))
          - ($XI * pow($E - $E0, 3.0))
          + ($XII * pow($E - $E0, 5.0))
          - ($XIIA * pow($E - $E0, 7.0));
 
      return new LatLng(rad2deg($phi), rad2deg($lambda));
    }
  }

  // ================================================================== RefEll

  class RefEll {

    var $maj;
    var $min;
    var $ecc;


    /**
     * Create a new RefEll object to represent a reference ellipsoid
     *
     * @param maj the major axis
     * @param min the minor axis
     */
    function RefEll($maj, $min) {
      $this->maj = $maj;
      $this->min = $min;
      $this->ecc = (($maj * $maj) - ($min * $min)) / ($maj * $maj);
    }
  }


  // ================================================== Mathematical Functions

  function sinSquared($x) {
    return sin($x) * sin($x);
  }

  function cosSquared($x) {
    return cos($x) * cos($x);
  }

  function tanSquared($x) {
    return tan($x) * tan($x);
  }

  function sec($x) {
    return 1.0 / cos($x);
  }
?>