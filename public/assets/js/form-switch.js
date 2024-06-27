// reset the previously clicked buttons to normal state
function resetClickedBtns(clickedButtons) {
  if (clickedButtons.length > 0) {
    clickedButtons.forEach(function (button) {
      button.classList.remove("current-button");
      button.removeAttribute("disabled");
    });
  }
}

// set the hidden input value of type of step
function setHiddenStepType(button) {
  let inputType = document.getElementById("type-form");
  let newTypeValue = button.split("-")[0];

  inputType.value = newTypeValue;
}

// reset the title inner text of the file input
function resetInnerTextFileInput(createSwitchForms) {
  if (createSwitchForms.length > 0) {
    createSwitchForms.forEach(function (form) {
      inputs = form.querySelectorAll("input");
      inputs.forEach(function (input) {
        // if input is of file type
        if (input.type === "file") {
          let label = input.nextElementSibling; // label of the file input

          labelInnerText = label.querySelector(".file-input-text"); // get the inner element displaying file info of the input
          labelInnerText.innerText = "Select File";
        }
      });
    });
  }
}

/**
 * Swith to the new form type
 */
function switchForm(formType, button) {
  let parentForm = document.querySelector("#switch-form-parent");
  let createSwitchForms = document.querySelectorAll(".switch-form"); // all switchable forms
  let buttonClicked = document.querySelector(`#${button}`); // button clicked

  let clickedButtons = document.querySelectorAll(".current-button"); // previously clicked buttons

  resetClickedBtns(clickedButtons);

  parentForm.reset(); // reset the whole form

  setHiddenStepType(button);

  resetInnerTextFileInput(createSwitchForms);

  // change clicked button style
  buttonClicked.classList.add("current-button");
  buttonClicked.setAttribute("disabled", "disabled");

  for (let i = 0; i < createSwitchForms.length; i++) {
    if (createSwitchForms[i].classList.contains("display")) {
      createSwitchForms[i].classList.remove("display");
    }
  }

  let form = document.querySelector(`.${formType}`);

  form.classList.toggle("display");
}
