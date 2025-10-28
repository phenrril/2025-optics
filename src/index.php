<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "nueva_venta";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}
include_once "includes/header.php";
?>

<style> 
/* Estilos modernos para Notas */
.notas-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.page-header-notas {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    text-align: center;
}

.page-header-notas h2 {
    margin: 0;
    font-weight: 600;
    font-size: 2rem;
}

#app {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
    padding: 25px 0;
}

.note {
    min-height: 280px;
    box-sizing: border-box;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    resize: vertical;
    font-family: 'Poppins', sans-serif;
    font-size: 15px;
    line-height: 1.6;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    transition: all 0.3s ease;
}

.note:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
    background: #fff;
}

.add-note {
    min-height: 280px;
    border: 2px dashed #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    border-radius: 15px;
    font-size: 48px;
    color: #667eea;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    font-weight: 300;
}

.add-note:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-color: #764ba2;
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
}

.add-note::before {
    content: '+';
    font-size: 80px;
    line-height: 1;
    margin-bottom: 10px;
}

.empty-state-notas {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-notas i {
    font-size: 5rem;
    color: rgba(102, 126, 234, 0.3);
    margin-bottom: 20px;
}

.fade-in-container {
    animation: fadeIn 0.6s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    #app {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .page-header-notas h2 {
        font-size: 1.5rem;
    }
}
</style>

<div class="notas-container fade-in-container">
    <div class="page-header-notas">
        <h2><i class="fas fa-sticky-note mr-2"></i> Notas Rápidas</h2>
        <p class="mb-0 mt-2"><i class="fas fa-info-circle mr-1"></i> Crea y gestiona tus notas de manera intuitiva</p>
    </div>

    <div id="app">
        <button class="add-note" type="button" title="Agregar Nueva Nota"></button>
    </div>
</div>



<script>
const notesContainer = document.getElementById("app");
const addNoteButton = notesContainer.querySelector(".add-note");

// Cargar notas existentes
const existingNotes = getNotes();
existingNotes.forEach((note) => {
  const noteElement = createNoteElement(note.id, note.content);
  notesContainer.insertBefore(noteElement, addNoteButton);
});

// Agregar listener para el botón
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
  element.placeholder = "Escribe tu nota aquí...";
  element.dataset.id = id;

  element.addEventListener("change", () => {
    updateNote(id, element.value);
  });

  element.addEventListener("dblclick", () => {
    const doDelete = confirm("¿Está seguro de borrar esta nota?");
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

  // Enfocar el nuevo elemento
  noteElement.focus();

  notes.push(noteObject);
  saveNotes(notes);
}

function updateNote(id, newContent) {
  const notes = getNotes();
  const targetNote = notes.filter((note) => note.id == id)[0];
  if (targetNote) {
    targetNote.content = newContent;
    saveNotes(notes);
  }
}

function deleteNote(id, element) {
  const notes = getNotes().filter((note) => note.id != id);
  saveNotes(notes);
  notesContainer.removeChild(element);
}
</script>
<?php include_once "includes/footer.php"; ?>