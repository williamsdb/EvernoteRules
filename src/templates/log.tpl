{include file="header.tpl"}

<h3>Log</h3>

<figure>
<table style="width: 100%" id="datatableResdb" class="display nowrap" cellspacing="0" width="100%">
<thead>
    <tr>
        <th>Date</th>
        <th>Log Entry</th>
        <th>Function</th>
    </tr>
</thead>
<tbody>
    {section name=all loop=$log}
    <tr>
        <td>{$log[all].date}</td>
        <td>{$log[all].entry}</td>
        <td>{$log[all].function}, {$log[all].position}</td>
    </tr>
    {/section}
</tbody>
<tfoot>
    <tr>
        <th>Date</th>
        <th>Log Entry</th>
        <th>Function</th>
    </tr>
</tfoot>
</table>
<figcaption>&nbsp;</figcaption>
</figure>
{include file="footer.tpl"}