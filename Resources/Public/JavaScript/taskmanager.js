document.addEventListener("DOMContentLoaded", async function(e) {

  const form = document.querySelector('.gtdjs-permissioncheck-form');

  const response = await fetch(form.action, {
    method: form.method,
    body: new FormData(form),
  });

  const tasks = await response.json();

  for (const task of tasks) {
    console.log(task);
    if (task.toggleState) {
      document.querySelector('.gtdjs-taskitem[data-uid="' + task.uid + '"] .controls .toggleState').classList.remove('d-none');
    }
    if (task.modify) {
      document.querySelector('.gtdjs-taskitem[data-uid="' + task.uid + '"] .controls .modify').classList.remove('d-none');
    }
  }

});
