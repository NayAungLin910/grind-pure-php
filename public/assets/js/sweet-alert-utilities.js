// confirm the permanent delete
function confirmDelete(id, name, type) {
  Swal.fire({
    title: `Are you sure about deleteing the ${type}, ${name} permanently?`,
    text: "",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      let deleteForm = document.querySelector(`#delete-form-${id}`);
      deleteForm.submit();
    }
  });
}