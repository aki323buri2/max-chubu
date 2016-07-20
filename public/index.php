<?php
require_once __dir__.'/../php/functions.php';
$links = [
	'https://cdn.jsdelivr.net/jquery/3.1.0/jquery.min.js', 
	'https://cdn.jsdelivr.net/semantic-ui/2.2.2/semantic.min.css', 
	'https://cdn.jsdelivr.net/semantic-ui/2.2.2/semantic.min.js', 
	'https://cdn.jsdelivr.net/fontawesome/4.6.3/css/font-awesome.min.css', 
];
$links = array_group_by($links, function ($value, $key)
{
	return extension_without_dot($value);
});

$tokuscs = [
	121667, 
	133123, 
	128531, 
	176514, 
	122516, 
	198079, 
	169995, 
	197257, 
	167624, 
	189022, 
];

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>マックスバリュー中部？</title>

<?php foreach ((array)@$links['css'] as $url) {?>
	<link rel="stylesheet" href="<?php echo $url?>">
<?php }?>
<?php foreach ((array)@$links['js'] as $url) {?>
	<script src="<?php echo $url?>"></script>
<?php }?>
<style>
*
{
	font-family: Lato, Meiryo, arial;
}
#topbar
{
	margin-bottom: 1rem;
}
#sidebar
{
	float: left;
	width: 16rem;
}
#content
{
	margin-left: 16rem;
}

#tokusc-select > a.item.selected
{
	background: #ccc;
}

#table1 .tokusc
{
	width: 10rem;
}
#table1 tbody td > i:first-child
{
	margin-right: 1rem;
}
#table1 tbody tr.error
{
	background: red;
}
#table1 tbody tr.processing
{
	background: #ccc;
}
#table1 tbody tr.downloading
{
	background: #eee;
}
#table1 tbody tr.complete
{
	text-align: center;
	background: blue;
	color: #fff;
}
</style>
</head>

<body>
<div id="topbar">
	<div class="ui menu">
		<a href="/" class="header item">
			<i class="building icon"></i>
			マックスバリュー中部？？
		</a>
		<a href="#" class="item">
			<i class="home icon"></i>
			Home
		</a>

		<div class="ui right menu">
			<div class="item">
				<div class="ui search">
					<div class="ui icon input">
						<input type="text" class="prompt" placeholder="Search">
						<i class="search icon"></i>
					</div>
					<div class="results"></div>
				</div>
			</div>
			<a href="#" class="item">
				<i class="user icon"></i>
				Links
			</a>
		</div>
	</div>
</div>
<div id="sidebar">
	<div class="ui vertical menu" id="tokusc-select">
		<?php foreach ($tokuscs as $tokusc) {?>
			<a href="#" 
				class="item"
				data-value="<?php echo $tokusc?>"
			>
				<?php echo $tokusc?>
			</a>
		<?php }?>
	</div>
</div>

<?php
$values = [
	['tokusc', '取引先CD'], 
	['process', '進捗状況'], 
	['downloaded', '取得ファイル'], 
];
foreach ($values as $value)
{
	$columns[] = ['name'=>$value[0], 'title'=>$value[1]];
} 
?>

<div id="content">
	
	<div class="ui secondary menu">
		<a href="#" class="item"
			id="start-parallel"
		>
			<i class="cloud download icon"></i>
			並列処理開始
		</a>
		<a href="#" class="item"
			id="start-sequencial"
		>
			<i class="cloud download icon"></i>
			直列処理開始
		</a>
	</div>

	<table class="ui celled fixed table" id="table1">
		<thead>
			<tr>
				<?php foreach ($columns as $column) {?>
					<th
						class="<?php echo $column['name']?>"
						data-name="<?php echo $column['name']?>"
						data-title="<?php echo $column['title']?>"
					>
						<?php echo $column['title']?>
					</th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>

<script>
$(function ()
{
	var table = $('#table1');
	var select = $('#tokusc-select > a.item');
	var parallel = $('#start-parallel');
	var sequencial = $('#start-sequencial');

	select.on('click', selectTokuscClick);
	parallel.on('click', startParallelsClick);
	sequencial.on('click', startSequencialsClick);
	
	function selectTokuscClick()
	{
		var $this = $(this);
		var selected = 'selected';
		var tokusc = $this.data('value');

		if ($this.hasClass(selected))
		{
			selectTokusc(table, tokusc, false);
			$this.removeClass(selected);
		}
		else
		{
			selectTokusc(table, tokusc, true);
			$this.addClass(selected);
		}
	}
	function startParallelsClick()
	{
		startParallels(table);
	}
	function startSequencialsClick()
	{
		startSequencials(table);
	}
});
function selectTokusc(table, tokusc, selected)
{
	var tbody = table.find('tbody');

	tbody.find('tr.complete').remove();

	if (selected === false)
	{
		tbody.find('tr[tokusc=' + tokusc + ']').remove();
		return;
	}

	var tr = $('<tr>').appendTo(tbody).attr('tokusc', tokusc);
	
	<?php foreach ($columns as $column) {?>
		(function ()
		{
			var td = $('<td>')
				.appendTo(tr)
				.addClass('<?php echo $column['name']?>')
			;
		})();
	<?php }?>

	tr.find('td.tokusc').text(tokusc);
}
function startParallels(table)
{
	table.find('tbody > tr.complete').remove();

	table.find('tbody > tr')
		.removeClass('error processing downloading')
		.each(function ()
		{
			var tr = $(this);
			startParallel(tr);
		})
	;
}
function startParallel(tr)
{
	var tokusc = tr.attr('tokusc');
	var process = tr.find('td.process');
	var downloaded = tr.find('td.downloaded');

	tr.addClass('doing');

	step1();

	function step1()
	{
		tr.addClass('processing');
		process
			.text('データ集計中です・・・')
			.prepend($('<i>').addClass('fa fa-spinner fa-spin fa-pulse'))
		;
		$.ajax({
			url: 'process.php'
			, data: { tokusc: tokusc }
		})
		.done(function (data)
		{
			tr.removeClass('processing');
			process.text('データ集計が終了しました');
			downloaded.text(data);
			step2();
		})
		.fail(function (xhr, error, thrown)
		{
			process.text(thrown);
			tr.addClass('error');
		})
		;
	}
	function step2()
	{
		tr.addClass('downloading');
		process
			.text('データをダウンロード中です・・・')
			.prepend($('<i>').addClass('fa fa-spinner fa-spin fa-pulse'))
		;
		$.ajax({
			url: 'download.php'
			, data: { tokusc: tokusc }
		})
		.done(function (data)
		{
			tr.removeClass('downloading');
			process.text('ダウンロードが終了しました');
			downloaded.text(data);
			step3();
		})
		.fail(function (xhr, error, thrown)
		{
			process.text(thrown);
			tr.addClass('error');
		})
		;
	}
	function step3()
	{
		tr.removeClass('doing');

		if (tr.parent().find('tr.doing').length === 0)
		{
			onComplete(tr.closest('table'));
		}
	}
}
function startSequencials(table)
{
	var tbody = table.find('tbody');
	tbody.find('tr.complete').remove();
	tbody.find('tr').each(function ()
	{
		var tr = $(this);
	});
}
function onComplete(table)
{
	var tbody = table.find('tbody');
	var tr = $('<tr>').appendTo(tbody).addClass('complete');
	var td = $('<td>').appendTo(tr).attr('colspan', table.find('thead > tr > th').length).text('complete!!');
}
</script>
</body>
</html>