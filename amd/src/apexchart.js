
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

define([
  "jquery",
  "./chart/apexcharts",
  "./jquery.dataTables",
  "./dataTables.bootstrap4",
], function ($, ApexCharts) {
  return {
    init: function ($data, $linedata) {
      $("#enrolledusers").DataTable();

      let SELECTOR = {
        SHADES: ".siteaccess-values-shade .shades",
      };

      $(document).ready(function () {
        generateShades();

        var options = {
          series: $data,
          chart: {
            height: 500,
            type: "heatmap",
          },
          xaxis: {
            categories: [
              "Domingo",
              "Segunda",
              "Terça",
              "Quarta",
              "Quinta",
              "Sexta",
              "Sábado",
            ],
          },
          plotOptions: {
            heatmap: {
              shadeIntensity: 0,
              radius: 0,
              useFillColorAsStroke: true,
            },
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            width: 2,
            show: true,
          },
          title: {
            text: "Distribuição de autenticação no site por período do dia",
          },
          colors: ["#1babb1"],
        };
        chart = new ApexCharts(document.getElementById("heatmap"), options);
        chart.render();

        var options = {
          series: [
            {
              name: "Login",
              data: $linedata.data,
            },
          ],
          chart: {
            height: 350,
            type: "area",
          },
          forecastDataPoints: {
            count: 7,
          },
          stroke: {
            width: 1,
            curve: "smooth",
          },
          xaxis: {
            type: "datetime",
            categories: $linedata.series,
            tickAmount: 12,
            labels: {
              formatter: function (value, timestamp, opts) {
                return opts.dateFormatter(new Date(timestamp), "dd/MMM/y");
              },
            },
          },
          title: {
            text: "Distribuição de autenticação no Site",
            align: "left",
            style: {
              fontSize: "12px",
              color: "#666",
            },
          },
          fill: {
            type: "gradient",
            gradient: {
              shade: "dark",
              gradientToColors: ["#FDD835"],
              shadeIntensity: 1,
              type: "horizontal",
              opacityFrom: 1,
              opacityTo: 1,
              stops: [0, 100, 100, 100],
            },
          },
        };

        var chart = new ApexCharts(document.getElementById("linemap"), options);
        chart.render();
      });

      /**
       * Generate shades of heatmap.
       */
      function generateShades() {
        let opacity = 0;
        let numberOfShades = 15;
        let increment = 1 / (numberOfShades - 1);
        for (let index = 1; index <= numberOfShades; index++) {
          $(SELECTOR.SHADES).append(
            `<div class="shade" style="opacity: ${opacity};"><div class="shade-inner"></div></div>`
          );
          opacity += increment;
        }
        let width = 100 / numberOfShades;
        $(SELECTOR.SHADES)
          .find(".shade .shade-inner")
          .css("background-color", "#1babb1");
        $(SELECTOR.SHADES)
          .find(".shade")
          .css("width", width + "%");
      }
    },
  };
});
