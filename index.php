<?php
ob_start(); // Start output buffering
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start(); // Start the session

// Redirect only when guard is set, which is always and if it isn't move to the javascript and set it there, if a cookie is valid, otherwise restart site and redirect
if (isset($_SESSION['guard']) && $_SESSION['guard'] == false) {
  header('Location: main.php'); // Redirect to the login page
  exit;
}


ob_end_flush();
?>
<!DOCTYPE html>
<html>
<head>
  <script>
    if (sessionStorage.getItem('hasCodeRunBefore') != 'true') {
      let formData = new FormData();
      formData.append("action", "checkcookie");
      fetch(window.location.origin + "/api/userlogin.php", {
          method: "POST",
          body: formData,
          credentials: 'include' // Include cookies in the request
      })
      .then(response => response.json()) // Parse response as JSON
      .then(data => {
        if (data.status) {
          sessionStorage.setItem('hasCodeRunBefore', 'true');
          window.location.reload();
        } else {
          sessionStorage.setItem('hasCodeRunBefore', 'false');
          window.location.reload();
        }
      })
      .catch(error => {
          console.log("Error:", error);
      });
    }
  </script>
  <link rel="stylesheet" href="./styles/scheduler-settings.css">
  <link rel="stylesheet" href="./styles/scheduler.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" style="width: 0px;">
    <button onclick="toggleSidebar()">Back</button>
    <div id="manage-tasks" class="hidden">
      <button onclick="backToTodoLists()">Backto</button>
      <button onclick="addnew()" id="addtask">Add Item</button>
      <button onclick="createdraggable_new()" id="addtaskdd">Add Subcategory</button>
      <!-- These will be saved too, create an additional db -->
      <div class="container" id="sidebar-items">
      </div>
    </div>
    <div id="manage_todo_lists">
      <button onclick="addnewtodolist()">AddList</button>
      <button onclick="savelists()">Save Lists</button>
      <div id="todo-lists"></div>
    </div>
  </div>
  <div id="settings">
  <div onclick="logout()">
  <svg class="icon" fill="none" stroke="currentColor" height="24"  viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
    <path d="M17 16L21 12M21 12L17 8M21 12L7 12M13 16V17C13 18.6569 11.6569 20 10 20H6C4.34315 20 3 18.6569 3 17V7C3 5.34315 4.34315 4 6 4H10C11.6569 4 13 5.34315 13 7V8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" fill="transparent"/>
  </svg>
  </div>
  </div>
  <!-- <div id="settings">
    <svg id="showSettingsDialog" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
      <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
    </svg>
  </div> -->
  <!-- <div id="edit">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
      <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
    </svg>
  </div> -->
  <div id="Hamburgur" onclick="toggleSidebar()">
    <svg height="32px" style="enable-background:new 0 0 32 32;" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      <path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,10z M28,14H4c-1.104,0-2,0.896-2,2 s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2 S29.104,22,28,22z"/>
    </svg>
  </div>
  <div id="modal-blackout"></div>
  <div id="settingsDialog">
    <div id="colorpicker">
      <div id="colorpickerside">
        <canvas id='canvas'></canvas>
        <div id="sidepicker"></div>
      </div>
      <div id='colorpreview'>here</div>
    </div>
    <form>
      <div>
        <button id="loginCancel">Cancel</button>
        <button id="confirmBtn" value="default">Confirm</button>
        <button id="registerBtn">Register</button>
      </div>
    </form>
  </div>
  <!-- Main Container -->
  <div id="main-container">
  </div>
  <script src="scheduler.js"></script>
  <script src="modal-settings.js"></script>
  <script src="colorwheel.js"></script>
  <script>

    function saveall() {
      // all that have a data-id are from database will need to save what I got from the server on the server will need to check if all ids are owned by the user (could have been changed to destroy someones data)
      // I can save them on the browser or call to database gain
      // I am going to call the database again and do differenciation here javascript is easier to work with + in production setting I would not want to load the server uneccesarily

      let dataobj = {
        selection: [],
        display: []
      };

      let sidebar_selection = document.getElementById("sidebar-items").children;

      let maincontainer = document.getElementById("main-container");
      let todoitem = maincontainer.children;

      Array.from(sidebar_selection).forEach(item => {
        // Your code here
        let obj = {
          ListSubcategoryID: parseInt(item.getAttribute("data-id")),
          SubcategoryName: item.querySelector('p').textContent
        };

        dataobj.selection.push(obj);
      });

      Array.from(todoitem).forEach(item => {
        // Your code here
        let obj = {
          itemId: isNaN(parseInt(item.getAttribute("data-id"))) ? null : parseInt(item.getAttribute("data-id")),
          itemName: item.querySelector("p").textContent,
          subcategories: []
        };

        let subcategories = Array.from(item.children);
        subcategories.shift(); // Remove first child

        subcategories.forEach((subcategory, index) => {
          let subcategoryObj = {
            subcategoryId: isNaN(parseInt(subcategory.getAttribute("data-id"))) ? null : parseInt(subcategory.getAttribute("data-id")),
            subcategoryName: subcategory.querySelector('p').textContent,
            subcategoryOrder: index
          };

          obj.subcategories.push(subcategoryObj);
        });
        dataobj.display.push(obj);
      });

      console.log("data object");
      console.log(dataobj);

      fetch('/api/app.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'saveall', data: dataobj, ListID: sessionStorage.getItem('currentTodoListNumber') })
      })
        .then(response => response.json())
        .then(data => {
          console.log(data);
        })
        .catch(error => {
          // Handle any errors here
          console.error(error);
        });

      maincontainer.innerHTML = "";
    }

        // Create a <p> tag with class "draggable" and draggable set to true
    function dynamicallycreateallfromdb(dataobject) {
      //selection from sidebar // will not be implemented
      // const sidebarItemsContainer = document.getElementById("sidebar-items");
      // sidebarItemsContainer.innerHTML = "";
      // dataobject.selection.forEach(item => {
      //   createdraggable_sidebar( sidebarItemsContainer,item)
      // });

      //create todoitems and its subcategories

      if (typeof dataobject.display === 'undefined') {
        return;
      }

      dataobject.display.forEach(item => {
    const main = document.getElementById('main-container');
    let div = document.createElement("div");
    div.setAttribute('data-id', item.itemId); // Set data-id attribute
    div.classList.add('container');
  
    
   
    
    let pTag = document.createElement("p");
    pTag.textContent = item.itemName;
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

        // Create a button element for removing



    item.subcategories.forEach(subcategory => {
      createdraggable(div,subcategory)
        });
    
        main.appendChild(div)
        });

    }
 


    function generateTodoLists() {
      // Dynamically generate content
      let formData = new FormData();
      formData.append("action", "gettodolists");
      
      fetch(window.location.origin + "/api/app.php", {
        method: "POST",
        body: formData,
        credentials: 'include' // Include cookies in the request
      })
      .then(response => {
        if (response.status === 404) {
          return; // Do not continue if response is 404
        }
        return response.json();
      })
      .then(data => {

        if (!data) {
          return; // Do not continue if data is empty
        }
        const container = document.getElementById("todo-lists");
        container.innerHTML = "";

      
        
        data.forEach(todoItem => {
          const wrapperDiv = document.createElement("div"); // Create a wrapper div element
          const button = document.createElement("p"); // Create a button element
          button.onclick = handleClick; // Set the onclick event to the named function handleClick
          button.textContent = todoItem.ListName;

          button.setAttribute("data-id", todoItem.ListID);
          button.classList.add("todo-list"); // Add the class "todo-list"
  
          wrapperDiv.appendChild(button); // Append the button to the wrapper div
          createButtons_lists(wrapperDiv); // Create buttons for the wrapper div (edit and delete buttons)
          container.insertBefore(wrapperDiv, container.firstChild); // Insert the wrapper div before the first child of the container
        });
      })
      .catch(error => {
        console.log("Error:", error);
      });
    }

    async function savelists(){

      // get all todolists
      const todoListsContainer = document.getElementById("todo-lists");

      const todoListElements = todoListsContainer.getElementsByClassName("todo-list");
      
      // all new ones will be form the top
      // edits will be all over the place

      // save their elements to change ids in dom later
      let newones = [];
      // save their names that have to be saved
      let newonesnames = [];
      // check old ones
      let checkoldones = [];

      for (let i = 0; i <  todoListElements.length; i++) {
        const child = todoListElements[i];
        // check if you should break by getting into ones that are already in the db
        const listId = child.getAttribute("data-id");
        if (child.getAttribute("data-id-new") != null) {
          newones.push(child); 
          newonesnames.push(child.textContent); // these will be sent out as additions
          continue;
        }
        checkoldones.push({
          listId: listId,
          textContent: child.textContent
        });
      }

      console.log(todoListsContainer.children)




// Call to savenew_or_update
let saveData = {
  action: "savenew_or_update",
  ListNameArray: newonesnames,
  ListNameArrayOld: checkoldones
};

await fetch(window.location.origin + "/api/app.php", {
  method: "POST",
  body: JSON.stringify(saveData),
  headers: {
    "Content-Type": "application/json"
  },
  credentials: 'include' // Include cookies in the request
})
.then(response => response.json())
.then(data => {
  newones.forEach((child, index) => {
    const lastInsertedIds = data.savedListIds;
    const listId = lastInsertedIds[index];
    child.removeAttribute('data-id-new');
    child.setAttribute("data-id", listId);
  });

  console.log(data)
})
.catch(error => {
  console.log("Error:", error);
});
    }

    async function handleClick() {
      // should hide only if call is successful we assume it is, because 
      const manageTodoLists = document.getElementById("manage_todo_lists");
      manage_todo_lists.classList.add("hidden");

      const managetasks = document.getElementById("manage-tasks");
      managetasks.classList.remove("hidden");
      // save alll lists
      await savelists()

let listId = this.getAttribute("data-id"); // get current
// get list of tasks
let formData = new FormData();
formData.append("action", "getitemsintodolist");
formData.append("ListID", listId);
fetch(window.location.origin + "/api/app.php", {
  method: "POST",
  body: formData,
  credentials: 'include' // Include cookies in the request
})
.then(response => response.json())
.then(data => {
  sessionStorage.setItem("currentTodoListNumber", listId);
  dynamicallycreateallfromdb(data);
});



}

    function backToTodoLists() {
      saveall();
      sessionStorage.removeItem("currentTodoListNumber");
      const manageTodoLists = document.getElementById("manage_todo_lists");
      manage_todo_lists.classList.remove("hidden");

      const managetasks = document.getElementById("manage-tasks");
      managetasks.classList.add("hidden");
    }


    // const wrapperDiv = document.createElement("div"); // Create a wrapper div element
    //       const button = document.createElement("p"); // Create a button element
    //       button.onclick = handleClick; // Set the onclick event to the named function handleClick
    //       button.textContent = todoItem.ListName;

    //       button.setAttribute("data-id", todoItem.ListID);
    //       button.classList.add("todo-list"); // Add the class "todo-list"
  
    //       wrapperDiv.appendChild(button); // Append the button to the wrapper div
    //       createButtons_lists(wrapperDiv); // Create buttons for the wrapper div (edit and delete buttons)
    //       container.insertBefore(wrapperDiv, container.firstChild); // Insert the wrapper div before the first child of the container

    function addnewtodolist() {
      let ListName = prompt('Type list name');
      if (ListName == null || ListName == "") {
        return;
      }
      //onto adding buttons to this
      const TodoLists = document.getElementById("todo-lists");

      const wrapperDiv = document.createElement("div");

      const button = document.createElement("p");
      button.textContent = ListName;
      button.classList.add("todo-list");
      button.onclick = handleClick;

      // Get the value of data-id-new attribute
      let dataIdNew = 0;
      if (TodoLists.firstChild) {
        dataIdNew = TodoLists.firstChild.getAttribute("data-id-new");
        if (dataIdNew) {
          // Increment the value by one
          dataIdNew = parseInt(dataIdNew) + 1;
        } else {
          // Set the initial value to 1
          dataIdNew = 1;
        }
      }
      // Set the data-id-new attribute with the updated value
      button.setAttribute("data-id-new", dataIdNew);

      wrapperDiv.appendChild(button);
      createButtons_lists(wrapperDiv);
   
      TodoLists.insertBefore(wrapperDiv, TodoLists.firstChild);
    }

    generateTodoLists();
  </script>
  <script>
    // create color wheel
    function logout(){
      let formData = new FormData();
        formData.append("action", "destroysession");
        fetch(window.location.origin + "/api/userlogin.php", {
          method: "POST",
          body: formData,
          credentials: 'include' // Include cookies in the request
        })
        .then(response => response.json()) // Parse response as JSON
        .then(data => {
          if (data.status) {
            sessionStorage.setItem('hasCodeRunBefore', 'false');
            window.location.href = 'logout.php'; // Redirect to the login page
          }
        })
        .catch(error => {
          alert('Odhlášení neúspěšné');
          console.log("Error:", error);
        });
    }
    // createcolorpicker('canvas', 'sidepicker', 'colorpreview');
  </script>
</body>
</html>
