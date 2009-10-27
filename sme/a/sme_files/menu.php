<style type="text/css">
table.menu {
	border:1px solid black;
}
table.menu td{
	border:1px solid black;
}

table.menu td.selected{
	background:#ccc;
}

</style>


<table class="menu">
<tr>
<td class="<? if($thisscript=='admin.php') echo 'selected';?>">
<a href="admin.php?token=<?=$token?>" >Files</a>
</td>
<td class="<? if($thisscript=='indexpr.php') echo 'selected';?>">
<a href="indexpr.php?token=<?=$token?>" >Providers</a>
</td>
<td class="<? if($thisscript=='indexie.php') echo 'selected';?>">
<a href="indexie.php?token=<?=$token?>" >Import / Export</a>
</td>
<td class="<? if($thisscript=='indexgr.php') echo 'selected';?>">
<a href="indexgr.php?token=<?=$token?>" >Groups</a>
</td>
</tr>
</table>