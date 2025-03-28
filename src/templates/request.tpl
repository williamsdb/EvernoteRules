{include file="header.tpl"}

<h3>Requests</h3>

<figure>
<table style="width: 100%" id="datatableResdb" class="display nowrap" cellspacing="0" width="100%">
<thead>
    <tr>
        <th>Date</th>
        <th>Request Entry</th>
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
        <th>Request Entry</th>
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
            "order": [[0, 'desc']],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [5, 10, 25, 50, "All"]],
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