
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
   * Chart.js
   * 
   * Copyright (c) 2014-2023 Chart.js Contributors
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
], function ($, ApexCharts) {
  return {
    init: function ($loginData) {
      var dates = $loginData.map((login) => new Date(login.login_date_with_time));
      var loginCount = {};
      dates.forEach((date) => {
        var dateString = date.toISOString().split("T")[0];
        loginCount[dateString] = (loginCount[dateString] || 0) + 1;
      });

      var newChartData = {
        series: [
          {
            name: "Logins",
            data: Object.values(loginCount),
          },
        ],
        chart: {
          height: 350,
          type: "line",
          zoom: {
            enabled: false,
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "straight",
        },
        title: {
          text: "Login History",
          align: "left",
        },
        grid: {
          row: {
            colors: ["#f3f3f3", "transparent"],
            opacity: 0.5,
          },
        },
        xaxis: {
          categories: Object.keys(loginCount).map((date) => {
            var dateObj = new Date(date);
            return `${dateObj.getMonth() + 1}-${dateObj.getDate()}`;
          }),
        },
        yaxis: {
          labels: {
            formatter: function (value) {
              return parseInt(value);
            },
          },
        },
      };
      
      var newChart = new ApexCharts(
        document.querySelector("#login-chart"),
        newChartData
      );
      newChart.render();
    },
  };
});