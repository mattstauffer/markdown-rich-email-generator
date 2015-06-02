<td>
  <table>
    <tr>
      <td class="wrapper last">
        <table class="twelve columns">
        <tr>
          <td>
            <ul class="postlist">
            @foreach ($section as $post)
              <li>
                {!! $post !!}
              </li>
            @endforeach
            </ul>
          </td>
          </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
