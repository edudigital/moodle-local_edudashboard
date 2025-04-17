
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Plugin administration pages are defined here.
 *
 * @package     local_edudashboard
 * @category    admin
 * @copyright   2025 edudigital <geral@edudigital-learn.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */  

  // This file is part of Moodle - http://moodle.org/
  //
  // Moodle is free software: you can redistribute it and/or modify
  // it under the terms of the GNU General Public License as published by
  // the Free Software Foundation, either version 3 of the License, or
  // (at your option) any later version.
  //
  // Moodle is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU General Public License for more details.
  //
  // You should have received a copy of the GNU General Public License
  // along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
  //
  /**
   * Plugin version and other meta-data are defined here.
   *
   * @package     local_edudashboard
   * @copyright   2025 edudigital <geral@edudigital-learn.com>
   * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
   */


define([
  "jquery",
  "./chart/apexcharts",
  "./jquery.dataTables",
  "./dataTables.bootstrap4",
], function ($, ApexCharts) {
  return {
    init: function ($labels, $data, $title) {
      $("#enrolledusers, .student-courses").DataTable();
      $(document).ready(function () {
        var options = {
          series: $data[0].data,
          chart: {
            height: 480,
            width: 480,
            type: "donut",
          },
          title: {
            text: $title,
            align: "center",
            margin: 20,
            offsetX: 0,
            offsetY: 0,
            floating: false,
            style: {
              fontSize: "14px",
              fontWeight: "bold",
              fontFamily: undefined,
              color: "#263238",
            },
          },
          legend: {
            position: "bottom",
            horizontalAlign: "center",
          },
          labels: $labels,
          responsive: [
            {
              breakpoint: 100,
              options: {
                chart: {
                  width: "100%",
                },
              },
            },
          ],
        };

        var chart = new ApexCharts(
          document.getElementById("apex-chart-doghnut"),
          options
        );
        chart.render();
      });
    },
  };
});
