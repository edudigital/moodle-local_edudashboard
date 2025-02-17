
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
   *
   * @package     local_edudashboard
   * @copyright   2025 edudigital <geral@edudigital-learn.com>
   * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
   */



define(["jquery"], function ($) {
  return {
    init: function ($download, $data, $container_id) {
      $(document).ready(function () {
        Highcharts.chart($container_id, {
          title: {
            text: $data.charttitle,
          },
          xAxis: {
            categories: $data.xAxis_categories,
          },
          yAxis: {
            min: 0,
            title: {
              text: $data.chartyAxistitle,
            },
          },
          labels: {
            items: [
              {
                html: $data.chartsubtitle,
                style: {
                  left: "50px",
                  top: "18px",
                  color:
                    // theme
                    (Highcharts.defaultOptions.title.style &&
                      Highcharts.defaultOptions.title.style.color) ||
                    "black",
                },
              },
            ],
          },
          series: $data.series,
        });
      });
    },
  };
});
