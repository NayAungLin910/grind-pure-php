let fileInputs = document.querySelectorAll(".file-input"); // get all file inputs

Array.prototype.forEach.call(fileInputs, (input) => {
  // loop through each file input
  let label = input.nextElementSibling;
  let labelValue = label.innerHTML;

  input.addEventListener("change", (e) => {
    let inputFileText = "";

    if (input.files && input.files.length > 1) {
      // if the uploaded files exist and is more than one

      inputFileText = input.getAttribute("data-multiple-caption") || "";

      inputFileText = inputFileText.replace("{count}", input.files.length);
    } else {
      inputFileText = e.target.value.split("\\").pop(); // get the single file name uploaded
    }

    labelInnerText = label.querySelector('.file-input-text'); // get the inner element 

    labelInnerText.innerText = inputFileText;
  });
});
