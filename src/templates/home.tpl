{include file="header.tpl"}

{if $oauth == ''}
	<p>Before you can use Evernote Rules you must connect your Evernote account. You can do this by pressing the button below.</p>
	<a href="/oauth"><img src="/img/connect_evernote_button.png" width="358" height="50"></a>
{else}
	<figure>
	<table style="width: 100%">
	<thead>
		<tr>
			<th>Name</th>
			<th>Type</th>
			<th>Notebook</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		{section name=all loop=$rules}
		<tr>
			<td>{$rules[all].ruleName}</td>
			<td>{$rules[all].type}</td>
			<td>{$rules[all].notebookName}</td>
			<td>
			<a href="/editRule/{$smarty.section.all.index}">Edit</a>
			<a href="#" onclick="confirmRedirect('/deleteRule/{$smarty.section.all.index}'); return false;">Delete</a>
			</td>
		</tr>
		{/section}
	</tbody>
	<tfoot>
		<tr>
			<th>Name</th>
			<th>Type</th>
			<th>Notebook</th>
			<th>Actions</th>
		</tr>
	</tfoot>
	</table>
	<figcaption>&nbsp;</figcaption>
	</figure>
	<a class="button" href="/addRule">Add a new rule</a>
{/if}

{include file="footer.tpl"}