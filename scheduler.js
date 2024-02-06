function changecolors(){
    
}




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
  const main = document.getElementById('main-container');
  let div = document.createElement("div");
  div.textContent = foo;
  div.classList.add('container');


  div.addEventListener('dragover', e => {
    e.preventDefault()
    const afterElement = getDragAfterElement(div, e.clientY)
    const draggable = document.querySelector('.dragging')

    //check
    if (afterElement == null) {
      div.appendChild(draggable)
    } else {
      div.insertBefore(draggable, afterElement)
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

function createDrag(){
  const sidebar = document.querySelector('.sidebar');
  let foo = prompt('Type here');
  let div = document.createElement("p");
  div.textContent = foo;
  div.classList.add('draggable');
  div.draggable = true;

  div.addEventListener('dragstart', () => {
    div.classList.add('dragging')
  })

  div.addEventListener('dragend', () => {
    div.classList.remove('dragging')
  })
  sidebar.appendChild(div);
}