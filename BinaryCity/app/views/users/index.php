<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Binary City Contacts</title>
  <link rel="stylesheet" href="/BinaryCity/public/assets/style.css">
</head>
<body>

<div class="container">
  <h1>Binary City Contact Manager</h1>

  <div class="tabs">
    <button class="tab-button active" onclick="showTab('create')">Create Contact</button>
    <?php if (!empty($users)): ?>
      <button class="tab-button" onclick="showTab('list')">All Clients</button>
      <button class="tab-button" onclick="showTab('link')">Link Clients</button>
      <button class="tab-button" onclick="showTab('manage')">Manage Linked</button>
    <?php endif; ?>
  </div>

  <div class="tab-content" id="tab-create">
    <form id="create-form">
      <label>Surname: <input name="surname" required></label>
      <label>Name: <input name="name" required></label>
      <label>Email: <input type="email" name="email" required></label>
      <button type="submit">Create User</button>
    </form>
    <div id="alert"></div>
    <?php if (empty($users)): ?>
      <p class="note">No users</p>
    <?php endif; ?>
  </div>

  <div class="tab-content" id="tab-list" style="display:none;"></div>
  <div class="tab-content" id="tab-link" style="display:none;"></div>
  <div class="tab-content" id="tab-manage" style="display:none;"></div>
</div>

<script>
function showTab(id) {
  // Activate tab
  document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
  document.getElementById('tab-' + id).style.display = 'block';
  event.target?.classList.add('active');

  // Reload content for tabs other than create
  if (id !== 'create') {
    fetch('/BinaryCity/public/?action=getTabsAjax')
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          // Replace inner HTML of each tab section
          const temp = document.createElement('div');
          temp.innerHTML = data.html;

          ['tab-list', 'tab-link', 'tab-manage'].forEach(tabId => {
            const newContent = temp.querySelector('#' + tabId);
            if (newContent) {
              document.getElementById(tabId).innerHTML = newContent.innerHTML;
            }
          });

          // Reattach event listeners after DOM replace
          attachEventListeners();
        }
      });
  }
}


function extractTabContent(html, id) {
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.querySelector('#' + id).innerHTML;
}

document.getElementById('create-form').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  fetch('/BinaryCity/public/?action=createAjax', {
    method: 'POST',
    body: formData
  }).then(res => res.json()).then(data => {
    const alertBox = document.getElementById('alert');
    if (data.status === 'success') {
  alertBox.textContent = 'User created successfully!';
  alertBox.className = 'success';
  form.reset();

  // Replace entire tab buttons + content
  const temp = document.createElement('div');
  temp.innerHTML = data.html;

  // Update tab buttons (if new ones added)
  const newButtons = temp.querySelector('.tabs');
  if (newButtons) document.querySelector('.tabs').innerHTML = newButtons.innerHTML;

  // Update all tab contents
  ['list', 'link', 'manage'].forEach(id => {
    const section = temp.querySelector('#tab-' + id);
    if (section) document.getElementById('tab-' + id).innerHTML = section.innerHTML;
    document.getElementById('tab-' + id).style.display = 'none';
  });

  showTab('list');
}
 else {
      alertBox.textContent = data.message;
      alertBox.className = 'error';
    }
  });
});

function attachEventListeners() {
  // Link form
  const linkForm = document.getElementById('link-form');
  if (linkForm) {
    linkForm.addEventListener('submit', function (e) {
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
        });
    });
  }

  // Unlink buttons (event delegation)
  document.querySelectorAll('[data-unlink]').forEach(btn => {
  btn.addEventListener('click', function () {
    const [parent, child] = this.dataset.unlink.split('-');

    // confirmation
    if (!confirm('Are you sure you want to unlink this user?')) return;

    const formData = new FormData();
    formData.append('parent_id', parent);
    formData.append('child_id', child);

    fetch('/BinaryCity/public/?action=unlinkAjax', {
      method: 'POST',
      body: formData
    }).then(res => res.json()).then(resp => {
      if (resp.status === 'success') {
        showTab('manage');
      }
    });
  });
});

}

</script>
</body>
</html>
