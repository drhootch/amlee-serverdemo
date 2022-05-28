<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>قائمة التسجيلات</title>
	<!-- DataTables CSS library -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"/>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<!-- DataTables JS library -->
	<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<style type="text/css">
	.bs-example{
	margin: 20px;
	}
	</style>
</head>
<body style="text-align: right;direction: rtl;margin: 2%;">
	<div class="bs-page">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="page-header clearfix">
						<h2 class="text-center">عرض التسجيلات الإملائية</h2>
					</div>
					<table id="records" class="display" style="width:100%">
						<thead>
							<tr>
								<th>#</th>
								<th>النص</th>
								<th>المادة الصوتية</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>#</th>
								<th>النص</th>
								<th>المادة الصوتية</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>        
		</div>
	</div>
</body>
<script>
$(document).ready(function(){
	$('#records').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax": "server.php",
	   "language": {
				"url": 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Arabic.json',
			},
		"search": {
				"regex": true,
				"smart": false,
				//"caseInsen":false,
			},
		"order": [[0, 'desc']],
	});
});
</script>
</html>
