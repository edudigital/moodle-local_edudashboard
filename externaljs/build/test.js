
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

require(['local_edudashboard/lang_loader'], function(langLoader) {
    langLoader.loadLangStrings(function(lang) {
        Highcharts.setOptions({
            lang: lang // Define as strings traduzidas carregadas dinamicamente
        });

        (function(a) {
            "object" === typeof module && module.exports 
                ? (a["default"] = a, module.exports = a)
                : "function" === typeof define && define.amd
                    ? define("highcharts/modules/exporting", ["highcharts"], function(h) {
                        a(h);
                        a.Highcharts = h;
                        return a;
                    })
                    : a("undefined" !== typeof Highcharts ? Highcharts : void 0);
        })(function(a) {
            function h(a, b, t, n) {
                a.hasOwnProperty(b) || (a[b] = n.apply(null, t),
                "function" === typeof CustomEvent && window.dispatchEvent(new CustomEvent("HighchartsModuleLoaded", {
                    detail: { path: b, module: a[b] }
                })));
            }

            a = a ? a._modules : {};
            h(a, "Extensions/Exporting/ExportingDefaults.js", [a["Core/Globals.js"]], function(a) {
                return {
                    exporting: {
                        type: "image/png",
                        url: "https://export.highcharts.com/",
                        pdfFont: { normal: void 0, bold: void 0, bolditalic: void 0, italic: void 0 },
                        printMaxWidth: 780,
                        scale: 2,
                        buttons: {
                            contextButton: {
                                className: "highcharts-contextbutton",
                                menuClassName: "highcharts-contextmenu",
                                symbol: "menu",
                                titleKey: "contextButtonTitle",
                                menuItems: [
                                    "viewFullscreen",
                                    "printChart",
                                    "separator",
                                    "downloadPNG",
                                    "downloadJPEG",
                                    "downloadPDF",
                                    "downloadSVG"
                                ]
                            }
                        },
                        menuItemDefinitions: {
                            viewFullscreen: { textKey: "viewFullscreen", onclick: function() { this.fullscreen.toggle(); }},
                            printChart: { textKey: "printChart", onclick: function() { this.print(); }},
                            separator: { separator: !0 },
                            downloadPNG: { textKey: "downloadPNG", onclick: function() { this.exportChart(); }},
                            downloadJPEG: { textKey: "downloadJPEG", onclick: function() { this.exportChart({ type: "image/jpeg" }); }},
                            downloadPDF: { textKey: "downloadPDF", onclick: function() { this.exportChart({ type: "application/pdf" }); }},
                            downloadSVG: { textKey: "downloadSVG", onclick: function() { this.exportChart({ type: "image/svg+xml" }); }}
                        },

                        lang: lang // Agora a tradução é carregada dinamicamente
                    }
                };
            });
        });
    });
});
