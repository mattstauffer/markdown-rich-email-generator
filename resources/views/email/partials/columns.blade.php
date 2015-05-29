<td>
  <table>
    <tr>
      <td class="wrapper last">
        <table class="twelve columns">
        <tr>
          @foreach ($section as $column)
            <td class="four sub-columns">
              {!! $column !!}
            </td>
          @endforeach
          </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
