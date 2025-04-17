
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
  
  /**
   * @license
   * 
   * ApexCharts.js
   * 
   * Copyright (c) 2018-2023 ApexCharts
   * 
   * Permission is hereby granted, free of charge, to any person obtaining a copy
   * of this software and associated documentation files (the "Software"), to deal
   * in the Software without restriction, including without limitation the rights
   * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   * copies of the Software, and to permit persons to whom the Software is
   * furnished to do so, subject to the following conditions:
   * 
   * The above copyright notice and this permission notice shall be included in all
   * copies or substantial portions of the Software.
   * 
   * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
   * SOFTWARE.
   */

define(["jquery", "./chart/apexcharts","core/str"], function ($, ApexCharts) {
  return {
    init: function ($download, $data, $container_id) {
      $(document).ready(function () {
        var options = {
          series: $data.series,
          chart: {
            height: 350,
            type: "line",
          },
          stroke: {
            width: [0, 4],
          },
          title: {
            text: $data.charttitle,
          },
          dataLabels: {
            enabled: true,
            enabledOnSeries: [1],
          },
          labels: $data.xAxis_categories,
          yaxis: [
            {
              title: {
                text: $data.chartyleftAxistitle,
              },
            },
            {
              opposite: true,
              title: {
                text: $data.chartyrighttAxistitle,
              },
            },
          ],
        };
        var chart = new ApexCharts(
          document.getElementById($container_id),
          options
        );
        chart.render();
      });
    },
  };
});
