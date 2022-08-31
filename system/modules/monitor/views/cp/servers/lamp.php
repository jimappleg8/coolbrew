<th><?=$lamp['name'];?></th>
<td><?=$lamp['percent-disk-used'];?></td>
<td<?php if ((int)$lamp['percent-memory-used'] > 90): ?> style="background-color:red;"<?php endif; ?>><?=$lamp['percent-memory-used'];?></td>
<td><?=$lamp['percent-cpu-used'];?></td>
