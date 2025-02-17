
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
   * DataTables Bootstrap integration
   * 
   * Copyright (c) 2008-2023 SpryMedia Ltd.
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


!function(factory){"function"==typeof define&&define.amd?define(["jquery"],function($){return factory($,window,document)}):"object"==typeof exports?module.exports=function(root,$){return root||(root=window),$&&$.fn.dataTable||($=require("jquery.dataTables")(root,$).$),factory($,root,root.document)}:factory(jQuery,window,document)}(function($,window,document,undefined){"use strict";var DataTable=$.fn.dataTable;return $.extend(!0,DataTable.defaults,{dom:"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",renderer:"bootstrap"}),$.extend(DataTable.ext.classes,{sWrapper:"dataTables_wrapper container-fluid dt-bootstrap4",sFilterInput:"form-control form-control-sm",sLengthSelect:"form-control form-control-sm",sProcessing:"dataTables_processing card",sPageButton:"paginate_button page-item"}),DataTable.ext.renderer.pageButton.bootstrap=function(settings,host,idx,buttons,page,pages){var btnDisplay,btnClass,activeEl,api=new DataTable.Api(settings),classes=settings.oClasses,lang=settings.oLanguage.oPaginate,aria=settings.oLanguage.oAria.paginate||{},counter=0,attach=function(container,buttons){var i,ien,node,button;for(i=0,ien=buttons.length;i<ien;i++)if(button=buttons[i],$.isArray(button))attach(container,button);else{switch(btnDisplay="",btnClass="",button){case"ellipsis":btnDisplay="&#x2026;",btnClass="disabled";break;case"first":btnDisplay=lang.sFirst,btnClass=button+(page>0?"":" disabled");break;case"previous":btnDisplay=lang.sPrevious,btnClass=button+(page>0?"":" disabled");break;case"next":btnDisplay=lang.sNext,btnClass=button+(page<pages-1?"":" disabled");break;case"last":btnDisplay=lang.sLast,btnClass=button+(page<pages-1?"":" disabled");break;default:btnDisplay=button+1,btnClass=page===button?"active":""}btnDisplay&&(node=$("<li>",{class:classes.sPageButton+" "+btnClass,id:0===idx&&"string"==typeof button?settings.sTableId+"_"+button:null}).append($("<a>",{href:"#","aria-controls":settings.sTableId,"aria-label":aria[button],"data-dt-idx":counter,tabindex:settings.iTabIndex,class:"page-link"}).html(btnDisplay)).appendTo(container),settings.oApi._fnBindAction(node,{action:button},function(e){e.preventDefault(),$(e.currentTarget).hasClass("disabled")||api.page()==e.data.action||api.page(e.data.action).draw("page")}),counter++)}};try{activeEl=$(host).find(document.activeElement).data("dt-idx")}catch(e){}attach($(host).empty().html('<ul class="pagination"/>').children("ul"),buttons),activeEl!==undefined&&$(host).find("[data-dt-idx="+activeEl+"]").focus()},DataTable});
