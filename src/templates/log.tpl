{include file="header.tpl"}

<h3>Log</h3>

<figure>
<table style="width: 100%" id="datatableResdb" class="display nowrap" cellspacing="0" width="100%">
<thead>
    <tr>
        <th>Date</th>
        <th>Log Entry</th>
    </tr>
</thead>
<tbody>
    {section name=all loop=$log}
    <tr>
        <td>{$log[all].date}</td>
        <td>{$log[all].entry}</td>
    </tr>
    {/section}
</tbody>
<tfoot>
    <tr>
        <th>Date</th>
        <th>Log Entry</th>
    </tr>
</tfoot>
</table>
<figcaption>&nbsp;</figcaption>
</figure>
<script>
    jQuery(document).ready(function($) {
    $('#datatableResdb').DataTable({
            "columns": [
                { targets: 0, visible: true, searchable: false, className: 'never' },
                { targets: 1, orderData: 0 }
            ],
            "order": [[0, 'asc']],
            "pageLength": 10,
            "responsive": true,
            "paging":   true,
            "ordering": true,
            "searching": true,
            "info":     true
            });

    var table = $('#datatableResdb').DataTable();

    table.on('page.dt', function() {
            $('html, body').animate({
                scrollTop: $('#datatableResdb').offset().top
            }, 300); // Adjust speed as needed
    });

    });
</script>
{include file="footer.tpl"}