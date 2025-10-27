<?php include_once "includes/header.php";
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "nueva_venta";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}
?>

<h1 class="text-center">Notas</h1><br>

<div id="app">
  <button class="add-note" type="button">+</button>
</div>

<style> 
/* body {
  margin: 0;
  background: #009578;
} */
body { 
	width: 100%;
	height:100%;
	font-family: 'Open Sans', sans-serif;
	background: #202020;
	background: -moz-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%),-moz-linear-gradient(top,  rgba(57,173,219,.25) 0%, rgba(42,60,87,.4) 100%), -moz-linear-gradient(-45deg,  #343a40 0%, #212529 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -webkit-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -webkit-linear-gradient(-45deg,  #343a40 0%,#212529 100%);
	background: -o-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -o-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -o-linear-gradient(-45deg,  #343a40 0%,#212529 100%);
	background: -ms-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -ms-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -ms-linear-gradient(-45deg,  #343a40 0%,#212529 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), linear-gradient(to bottom,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), linear-gradient(135deg,  #343a40 0%,#212529 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#202020', endColorstr='#212529',GradientType=1 );
}

h1 {
    color: #fff;
    font-size: 40px;
    font-weight: 300;
    text-align: center;
    margin: 0;
    padding: 20px 0;
}

#app {
  display: grid;
  grid-template-columns: repeat(auto-fill, 200px);
  padding: 24px;
  gap: 24px;
}

.note {
  height: 200px;
  box-sizing: border-box;
  padding: 16px;
  border: none;
  border-radius: 10px;
  box-shadow: 0 0 7px rgba(0, 0, 0, 0.15);
  resize: none;
  font-family: sans-serif;
  font-size: 16px;
}

.add-note {
  height: 200px;
  border: none;
  outline: none;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  font-size: 120px;
  color: rgba(0, 0, 0, 0.5);
  cursor: pointer;
  transition: background 0.2s;
}

.add-note:hover {
  background: rgba(0, 0, 0, 0.2);
}

</style>


<script>
const notesContainer = document.getElementById("app");
const addNoteButton = notesContainer.querySelector(".add-note");

getNotes().forEach((note) => {
  const noteElement = createNoteElement(note.id, note.content);
  notesContainer.insertBefore(noteElement, addNoteButton);
});

addNoteButton.addEventListener("click", () => addNote());

function getNotes() {
  return JSON.parse(localStorage.getItem("stickynotes-notes") || "[]");
}

function saveNotes(notes) {
  localStorage.setItem("stickynotes-notes", JSON.stringify(notes));
}

function createNoteElement(id, content) {
  const element = document.createElement("textarea");

  element.classList.add("note");
  element.value = content;
  element.placeholder = "Nueva Nota";

  element.addEventListener("change", () => {
    updateNote(id, element.value);
  });

  element.addEventListener("dblclick", () => {
    const doDelete = confirm(
      "¿Esta seguro de borrar la nota?"
    );

    if (doDelete) {
      deleteNote(id, element);
    }
  });

  return element;
}

function addNote() {
  const notes = getNotes();
  const noteObject = {
    id: Math.floor(Math.random() * 100000),
    content: ""
  };

  const noteElement = createNoteElement(noteObject.id, noteObject.content);
  notesContainer.insertBefore(noteElement, addNoteButton);

  notes.push(noteObject);
  saveNotes(notes);
}

function updateNote(id, newContent) {
  const notes = getNotes();
  const targetNote = notes.filter((note) => note.id == id)[0];

  targetNote.content = newContent;
  saveNotes(notes);
}

function deleteNote(id, element) {
  const notes = getNotes().filter((note) => note.id != id);

  saveNotes(notes);
  notesContainer.removeChild(element);
}
</script>
<?php include_once "includes/footer.php"; ?>