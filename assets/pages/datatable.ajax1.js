(function( $ ) {

	'use strict';

	var datatableInit = function() 
	{
		var $table = $('#datatable-ajax1');
		
		/*
		$table.dataTable({
			bProcessing: true,
			//bServerSide: true,
			// "iDisplayLength": 20,
			bFilter: true,
			sAjaxSource: $table.data('url'),
			bDeferRender: true,
			fnServerParams: function ( aoData ) 
			{
				aoData.push({ "name": "more_data", "value": "my_value" });
			}
		});
		*/
		
		
		$table.dataTable( 
		{
		//"lengthMenu": [[25, 50], [ 25, 50]],
		"processing": true,
		"serverSide": true,//Require for length request param
		"bDestroy": true,
		"bSort": false,
		"bFilter": true,
		"language": 
		{
			searchPlaceholder: $table.data('search_placeholder')
		},
		"ajax":
		{
			url : $table.data('url'), 
			type: "post",  
			//data:{'status':'02'},
			//data:$("#db_entry_form").serialize(),			
			"data": function(d)
			{
				d.form = $("#db_entry_form").serializeArray();
			},			
			error: function(data)
			{  
				console.log(data);
				/*$(".data-grid-error").html("");
				$("#data-grid").append('<tbody class=""><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#data-grid_processing").css("display","none");		*/		
			},
			/*success: function(data)
			{
				console.log(data);
			}*/
		}
		});		
		
	};

	$(function() 
	{
		datatableInit();
		
		//filter from datatable based on parameter
		$("#save_button").click(function()
		{
			datatableInit();
		});	
		
	});

}).apply( this, [ jQuery ]);