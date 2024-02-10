function changecolors(){
    
}

function createButtons(div) {
  // Create a div wrapper for the buttons
  const buttonWrapper = document.createElement('div');
  buttonWrapper.classList.add('button-wrapper');

  // Create a button element for removing
  const removeButton = document.createElement('button');
  removeButton.textContent = 'X';
  removeButton.classList.add('remove-button');
  removeButton.addEventListener('click', () => {
    div.remove();
  });
  buttonWrapper.appendChild(removeButton);

  // Create a button element for changing text content
  const changeTextButton = document.createElement('button');
  changeTextButton.textContent = 'Change Text';
  changeTextButton.classList.add('change-text-button');
  changeTextButton.addEventListener('click', () => {
    const newText = prompt('Enter new text:');
    if (newText !== null) {
      div.querySelector('p').textContent = newText;
    }
  });
  buttonWrapper.appendChild(changeTextButton);

  // // Create a button element for marking as completed
  // const completeButton = document.createElement('button');
  // completeButton.textContent = 'Mark as Completed';
  // completeButton.classList.add('complete-button');
  // completeButton.addEventListener('click', () => {
  //   div.classList.toggle('completed');
  // });
  // buttonWrapper.appendChild(completeButton);

  // Append the button wrapper to the div
  div.appendChild(buttonWrapper);
}

function createButtons_item(div) {
  // Create a div wrapper for the buttons
  const buttonWrapper = document.createElement('div');
  buttonWrapper.classList.add('button-wrapper');

  // Create a button element for removing
  const removeButton = document.createElement('button');
  removeButton.textContent = 'X';
  removeButton.classList.add('remove-button');
  removeButton.addEventListener('click', () => {
    div.parentNode.remove();
  });
  buttonWrapper.appendChild(removeButton);

  // Create a button element for changing text content
  const changeTextButton = document.createElement('button');
  changeTextButton.textContent = 'Change Text';
  changeTextButton.classList.add('change-text-button');
  changeTextButton.addEventListener('click', () => {
    const newText = prompt('Enter new text:');
    if (newText !== null) {
      div.querySelector('p').textContent = newText;
    }
  });
  buttonWrapper.appendChild(changeTextButton);
  // Append the button wrapper to the div
  div.appendChild(buttonWrapper);
}

function createdraggable_new(){
const div = document.querySelector('.sidebar'); // sidebar
let foo = prompt('Type here');
if(foo == null && foo == ""){
return;
}
let childdiv = document.createElement("div");
childdiv.setAttribute('data-id', null); // Set data-id attribute to null for db

childdiv.classList.add('draggable');
childdiv.draggable = true;

childdiv.addEventListener('dragstart', () => {
childdiv.classList.add('dragging')
})


childdiv.addEventListener('dragend', () => {
childdiv.classList.remove('dragging')
})

let p = document.createElement("p");
p.textContent = foo;
childdiv.appendChild(p);

createButtons(childdiv);

div.appendChild(childdiv);
}
function createdraggable(div,subcategory){
let childdiv = document.createElement("div");
childdiv.setAttribute('data-id', subcategory.subcategoryId); // Set data-id attribute
childdiv.setAttribute('data-order', subcategory.subcategoryOrder); // Set data-order attribute

childdiv.classList.add('draggable');
childdiv.draggable = true;

childdiv.addEventListener('dragstart', () => {
childdiv.classList.add('dragging')
})


childdiv.addEventListener('dragend', () => {
childdiv.classList.remove('dragging')
})

let p = document.createElement("p");
p.textContent = subcategory.subcategoryName;
childdiv.appendChild(p);

createButtons(childdiv);

div.appendChild(childdiv);
}

function createdraggable_sidebar(div,subcategory){
let childdiv = document.createElement("div");
childdiv.setAttribute('data-id', subcategory.ListSubcategoryID); // Set data-id attribute
childdiv.classList.add('draggable');
childdiv.draggable = true;

childdiv.addEventListener('dragstart', () => {
childdiv.classList.add('dragging')
})


childdiv.addEventListener('dragend', () => {
childdiv.classList.remove('dragging')
})

let p = document.createElement("p");
p.textContent = subcategory.SubcategoryName;
childdiv.appendChild(p);

createButtons(childdiv);

div.appendChild(childdiv);
}

//draggables



function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const main = document.getElementById('main-container');
  if(sidebar.style.width === '0px'){
    sidebar.style.width = '250px';
    main.classList.add('sidebar-expanded');
  }else{
    sidebar.style.width = '0'
    main.classList.remove('sidebar-expanded');
  }
}

function addnew(){
  let foo = prompt('Type here');
  if(foo == null || foo == ""){
    return;
  }
  const main = document.getElementById('main-container');
    let div = document.createElement("div");
    div.setAttribute('data-id', null);
    div.classList.add('container');
  
    
   
    
    let pTag = document.createElement("p");
    pTag.textContent = foo;
    let wrapperDiv = document.createElement("div");
    wrapperDiv.appendChild(pTag);
    createButtons_item(wrapperDiv);
    div.appendChild(wrapperDiv);
    

    div.addEventListener('dragover', e => {
      e.preventDefault()
      afterElement = getDragAfterElement(div, e.clientY)
      draggable = document.querySelector('.dragging')

      //check
      if (afterElement == null) {
        div.appendChild(draggable)
      } else {
        div.insertBefore(draggable, afterElement)
      }
    })

    div.addEventListener('drop', e => {
      e.preventDefault()
      draggable = document.querySelector('.dragging')
        if (!draggable.getAttribute('data-order')) {
          draggable.setAttribute('data-id', null); // Set data-id attribute to null
        }
   
    })
    main.appendChild(div)

}


function getDragAfterElement(container, y) {
  const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')]

  return draggableElements.reduce((closest, child) => {
    const box = child.getBoundingClientRect()
    const offset = y - box.top - box.height / 2
    if (offset < 0 && offset > closest.offset) {
      return { offset: offset, element: child }
    } else {
      return closest
    }
  }, { offset: Number.NEGATIVE_INFINITY }).element
}
