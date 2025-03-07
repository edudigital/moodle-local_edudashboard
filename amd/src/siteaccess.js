
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

define(['jquery','core/ajax'],

  function($, ajax) {

    return {
        init: function() {

            $(document).ready(function() {

		    var promises = ajax.call([
		    	
		        { methodname: 'local_edudashboard_siteaccess', 'args': {'startdate': 'nonyet', 'enddate': 'nonyet'} }		       
		    ]);

			   promises[0].done(function(response) {
			       console.log('mod_wiki/pluginname is' + response);
			   }).fail(function(ex) {
			   	 console.log("Erorr Showing");
			     console.log(ex);
			   });
			  
				        
            });
        }
    }
}) 