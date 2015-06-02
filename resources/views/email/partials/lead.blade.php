@if (! empty($lead))
<table class="row header">
  <tr>
    <td class="center" align="center">
      <center>

        <table class="container">
          <tr>
            <td class="wrapper last">

              <table class="twelve columns">
                <tr>
                  <td class="eight sub-columns">
                    <span class="template-label">{!! $lead['content'] !!}</span>
                  </td>
                  <td class="four sub-columns" style="text-align: right;">
                    <span class="template-label"><a href=""><webversion>View on Web</webversion></a></span>
                  </td>
                </tr>
              </table>

            </td>
          </tr>
        </table>

      </center>
    </td>
  </tr>
</table>
@endif
