<?php ob_start(); ?>
<div id="tab-list">
  <?php if (empty($users)): ?>
    <p class="note">No users found</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Full Name</th>
          <th>Email</th>
          <th>Code</th>
          <th>No. of linked clients</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['surname'] . ' ' . $user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['code']) ?></td>
            <td><?= User::countChildren($user['id']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div id="tab-link">
  <form id="link-form">
    <label>Client:
      <select name="parent_id" required>
        <option value="">--Select Client--</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>"><?= "{$u['surname']} {$u['name']}" ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Contact:
      <select name="child_id" required>
        <option value="">--Select Contact--</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>"><?= "{$u['surname']} {$u['name']}" ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <button type="submit">Link</button>
  </form>
  <div id="link-message"></div>
</div>

<div id="tab-manage">
  <?php
  $hasChildren = false;
  foreach ($users as $parent): 
    $children = User::getChildren($parent['id']);
    if (empty($children)) continue;
    $hasChildren = true;
  ?>
    <h4><?= $parent['surname'] . ' ' . $parent['name'] ?> (<?= $parent['code'] ?>)</h4>
    <ul>
      <?php foreach ($children as $child): ?>
        <li>
          <?= $child['surname'] . ' ' . $child['name'] ?> (<?= $child['code'] ?>)
          <button type="button" data-unlink="<?= $parent['id'] ?>-<?= $child['id'] ?>">Unlink</button>

        </li>
      <?php endforeach; ?>
    </ul>
  <?php endforeach; ?>

  <?php if (!$hasChildren): ?>
    <p class="note">No linked Clients</p>
  <?php endif; ?>
</div>

<script>
document.getElementById('link-form')?.addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(e.target);

  fetch('/BinaryCity/public/?action=linkAjax', {
    method: 'POST',
    body: formData
  }).then(res => res.json())
    .then(resp => {
      const msg = document.getElementById('link-message');
      msg.textContent = resp.status === 'success'
        ? 'Contacts linked successfully!'
        : (resp.message || 'Link failed.');
      msg.className = resp.status === 'success' ? 'success' : 'error';

      if (resp.status === 'success') {
        setTimeout(() => showTab('manage'), 400);
      }
    })
    .catch(err => {
      console.error('Link AJAX failed:', err);
      document.getElementById('link-message').textContent = 'AJAX error occurred.';
    });
});

function unlinkUser(parent, child) {
  const formData = new FormData();
  formData.append('parent_id', parent);
  formData.append('child_id', child);

  fetch('/BinaryCity/public/?action=unlinkAjax', {
    method: 'POST',
    body: formData
  }).then(res => res.json()).then(data => {
    if (data.status === 'success') {
      showTab('manage');
    }
  });
}
</script>
