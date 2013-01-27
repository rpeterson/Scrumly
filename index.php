<!DOCTYPE html>
<html lang="en">
  <head>
	  <meta charset="utf-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	  <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Scrumly</title>
    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="/js/bootstrap.min.js"></script>
	<script src="http://www.myersdaily.org/joseph/javascript/md5.js"></script>
  </head>
  <body>
	<div class="navbar">
	  <div class="navbar-inner">
	    <a class="brand" href="">Scrumly
		<span class="badge badge-success">Story</span><br>
		<span class="badge badge-important">Defect</span>
		<span class="badge badge-info">Test</span>
		<span class="badge">Task</span>
	</a>
	    <ul id="user-select" class="nav">
	    </ul>
	  </div>
	</div>
	<div class="container-fluid">
	<div class="row-fluid">
<!--		<div class="span3">
			<h2>Someday</h2>
			<table id ="someday" class="table table-condensed table-bordered table-hover items">
			</table>
		</div>-->
		<div class="span4">
			<h2>Backlog</h2>
			<table id ="backlog" class="table table-condensed table-bordered table-hover items">
			</table>
		</div>
		<div class="span4">
			<h2>Current</h2>
			<table id ="in-progress"  class="table table-condensed table-bordered table-hover items">
			</table>
		</div>
		<div class="span4">
			<h2>Complete</h2>
			<table id="completed" class="table table-condensed table-bordered table-hover items">
			</table>
		</div>
<!--		<div class="span3">
			<h2>Accepted</h2>
			<table id ="accepted" class="table table-condensed table-bordered table-hover items">
			</table>
		</div>-->
	</div>
	</div>
	<div id="hidden-div" style="width:1px; height:1px; visibility:hidden; overflow:hidden">
	    <img src="/img/spinner.gif" />
	</div>
  </body>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$.ajax({
		  url: '/people.php',
		  type: 'GET',
		  dataType: 'json',
		  complete: function(xhr, textStatus) {
		    //called when complete
		  },
		  success: function(data, textStatus, xhr) {
			html = '';
			for (var i=0; i < data.length; i++) {
				if (data[i].revoked) {
					//This should be cleaned up once revoked goes live, but until then it doesnt exist as a parameter so we should just default to showing the users.
				}else{
					html += '<li><a class="user" data-user-id="'+data[i].id+'" href="#"><img alt="'+data[i].first_name+' '+data[i].last_name+'" title="'+data[i].first_name+' '+data[i].last_name+'" style="height:30px; width:30px;" src="http://www.gravatar.com/avatar/'+md5(data[i].email)+'"></a></li>';
				}
			};
			$('#user-select').html(html);
		  },
		  error: function(xhr, textStatus, errorThrown) {
		    //called when there is an error
		  }
		});

		$(document).on('click',
			'tr',
			function(e) {
				if ($(this).data('href')) {
					 window.open($(this).data('href'), '_blank');
				}
			}
		);
		$(document).on('click', '.user', function(e) {
			$('.active').removeClass('active');
			$(this).parent().addClass('active');
			e.preventDefault();
			types = ['someday','backlog','in-progress','completed','accepted'];
			for (var i=0; i < types.length; i++) {
				type = types[i];
				$('#'+type).html('<tr><td style="text-align: center;"><img src="/img/spinner.gif"></td></tr>');
				function table_populate_function(tis, type) {
					$.ajax({
					  url: '/items.php',
					  type: 'GET',
					  dataType: 'json',
					  data: {user_id: tis.data('user-id'),type: type},
					  success: function(data, textStatus, xhr) {
						html = '';
						if (data.length) {
							for (var j=0; j < data.length; j++) {
								console.log(data[j]);
								html += '<tr data-item-id="'+data[j].number+'" class="item ';
								if (data[j]['type']== 'story') {
									html += 'success'
								}
								if (data[j]['type'] == 'task') {
									html += ''
								}
								if (data[j]['type'] == 'defect') {
									html += 'error'
								}
								if (data[j]['type'] == 'test') {
									html += 'info'
								}
								html += '"><td style="font-weight:bold;">'+data[j].score+'</td>';
								html += '<td>'+data[j].title+' (<a href="'+data[j].short_url+'">#'+data[j].number+'</a>)</td></tr>';
							}
						}else{
							html = '<tr><td>No items</td></tr>';
						}
						$('#'+type).html(html);
					},
					  error: function(xhr, textStatus, errorThrown) {
						$('#'+type).html('');
					  }
					});
				}
				table_populate_function($(this), type);
			};
		});
		$(document).on('click', '.item', function(e) {
			$('.details').remove();
			$item = $(this);
			$.ajax({
				url: '/subitems.php',
				type: 'GET',
				dataType: 'json',
				data: {item_id: $(this).data('item-id')},
				success: function(data, textStatus, xhr) {
					if (data.length) {
						for (var j=0; j < data.length; j++) {
							console.log(data[j]);
							html = '<tr class="details">';
							html += '<td style="font-weight:bold;">'+data[j].score+'</td>';
							html += '<td>';
							switch (data[j].status) {
							case "backlog":
								html += '&#10007;';
								break;
							case "in-progress":
								html += '&#9658;';
								break;
							case "completed":
								html += '&#10003;';
								break;
							case "accepted":
								html += '&#9996;';
							}
							html += ' '+data[j].title+' (#'+data[j].number+')</td></tr>';
							$item.after(html);
						}
					}else{
						$item.after('<tr class="details"><td colspan="2">No sub-items found.</td></tr>');
					}
				},
				error: function(xhr, textStatus, errorThrown) {
				}
			});
		});
	});
</script>

</html>