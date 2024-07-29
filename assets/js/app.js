// Función para mostrar mensajes
function showMessage(message, type) {
  const messageDiv = document.getElementById("message");
  messageDiv.innerHTML = message;
  messageDiv.className = `message ${type}`;
  messageDiv.style.display = "block";
}

// Función para cargar configuraciones API desde la base de datos
function loadApiSettings() {
  fetch("api/get_api_settings.php") // Reemplaza con la ruta correcta
    .then((response) => response.json())
    .then((data) => {
      if (data.apiToken) {
        document.getElementById("api-token").value = data.apiToken;
      }

      if (data.contactListId) {
        document.getElementById("contact-list-id").value = data.contactListId;
      }
    })
    .catch((error) => console.error("Error al cargar configuraciones:", error));
}

// Cargar configuraciones al cargar la página
document.addEventListener("DOMContentLoaded", loadApiSettings);

// Función para validar el Token API
function validateApiToken() {
  const apiToken = document.getElementById("api-token").value;
  const contactListId = document.getElementById("contact-list-id").value;

  if (apiToken.trim() === "" || contactListId.trim() === "") {
    showMessage(
      "Por favor, ingrese el Token API y el ID de Lista de contacto.",
      "error"
    );
    return;
  }

  fetch(`https://api.hetrixtools.com/v3/contact-lists`, {
    headers: {
      Authorization: `Bearer ${apiToken}`,
    },
  })
    .then((response) => {
      if (response.status === 200) {
        return response.json();
      } else {
        throw new Error("Token API no válido.");
      }
    })
    .then((data) => {
      const contactList = data.contact_lists.find(
        (list) => list.id === contactListId
      );
      if (contactList) {
        showMessage("Token API y ID de Lista de contacto válidos.", "success");
      } else {
        throw new Error("ID de Lista de contacto no válido.");
      }
    })
    .catch((error) => showMessage(error.message, "error"));
}

// Función para agregar una nueva IP
function registerRow() {
  const name = document.getElementById("new-name").value;
  const ip = document.getElementById("new-ip").value;

  if (name.trim() === "" || ip.trim() === "") {
    alert("Por favor, complete todos los campos.");
    return;
  }

  const apiToken = localStorage.getItem("apiToken");
  const contactListId = localStorage.getItem("contactListId");

  if (!apiToken || !contactListId) {
    alert(
      "Por favor, configure el Token API y el ID de Lista de contacto primero."
    );
    return;
  }

  fetch("hetrix.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      action: "add",
      api_key: apiToken,
      target: ip,
      label: name,
      contact: contactListId,
    }),
  })
    .then((response) => response.text())
    .then((result) => {
      const table = document
        .getElementById("ip-table")
        .getElementsByTagName("tbody")[0];
      const rowCount = table.rows.length + 1;
      const newRow = table.insertRow();
      newRow.className = "main-row";

      const cellId = newRow.insertCell(0);
      const cellName = newRow.insertCell(1);
      const cellIp = newRow.insertCell(2);
      const cellStatus = newRow.insertCell(3);
      const cellListedIn = newRow.insertCell(4);
      const cellUpdated = newRow.insertCell(5);
      const cellActions = newRow.insertCell(6);

      cellId.innerHTML = rowCount;
      cellName.innerHTML = name;
      cellIp.innerHTML = ip;
      cellStatus.innerHTML = "Pendiente";
      cellListedIn.innerHTML = "";
      cellUpdated.innerHTML = "";
      cellActions.innerHTML = `
                <button class="btn-action" onclick="verifyRow(this)" title="Verificar ahora">
                    <img src="https://img.icons8.com/material-outlined/24/000000/user-shield.png"/>
                </button>
                <button class="btn-action" onclick="viewDetails(this)" title="Ver Detalles">
                    <img src="https://img.icons8.com/material-outlined/24/000000/eye.png"/>
                </button>
                <button class="btn-action" onclick="deleteRow(this)" title="Eliminar">
                    <img src="https://img.icons8.com/material-outlined/24/000000/delete.png"/>
                </button>`;

      const detailRow = table.insertRow();
      detailRow.className = "details-row";
      const detailCell = detailRow.insertCell(0);
      detailCell.colSpan = 7;
      detailCell.innerHTML = `
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>RBL</th>
                            <th>URL</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>`;

      document.getElementById("new-name").value = "";
      document.getElementById("new-ip").value = "";
      showMessage("IP agregada correctamente a HetrixTools.", "success");
    })
    .catch((error) => {
      showMessage(error.message, "error");
      console.error(error.message);
    });
}

// Función para ver detalles de una IP
function viewDetails(button) {
  const row = button.parentNode.parentNode;
  const detailRow = row.nextSibling;
  const apiToken = localStorage.getItem("apiToken");
  const ip = row.cells[2].innerText;

  if (!apiToken) {
    alert("Por favor, configure el Token API primero.");
    return;
  }

  if (detailRow.style.display === "none" || detailRow.style.display === "") {
    fetch(`https://api.hetrixtools.com/v3/blacklist-monitors`, {
      headers: {
        Authorization: `Bearer ${apiToken}`,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const detailsTable = detailRow.querySelector(".details-table tbody");
        detailsTable.innerHTML = "";

        const monitor = data.monitors.find((monitor) => monitor.target === ip);
        if (monitor && monitor.listed.length > 0) {
          monitor.listed.forEach((listing) => {
            const detailRow = detailsTable.insertRow();
            const cellRbl = detailRow.insertCell(0);
            const cellUrl = detailRow.insertCell(1);
            cellRbl.innerText = listing.rbl;
            cellUrl.innerHTML = `<a href="${listing.delist}" target="_blank">${listing.delist}</a>`;
          });
        } else {
          const detailRow = detailsTable.insertRow();
          const cell = detailRow.insertCell(0);
          cell.colSpan = 2;
          cell.innerText = "No hay listados para esta IP.";
        }

        detailRow.style.display = "table-row-group";
      })
      .catch((error) => {
        console.error(error.message);
        alert("Error al obtener los detalles de las listas negras.");
      });
  } else {
    detailRow.style.display = "none";
  }
}

// Función para convertir UTC a la zona horaria local en formato de 24 horas
const convertUTCToLocal = (utcDateStr) => {
  const date = new Date(utcDateStr + "Z"); // Añadimos 'Z' para indicar que la fecha está en UTC
  return date
    .toLocaleString("es-EC", {
      timeZone: "America/Guayaquil",
      hour12: false, // Esto asegura que la hora se muestra en formato de 24 horas
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
    })
    .replace(",", ""); // Eliminamos la coma entre la fecha y la hora
};

// Función para verificar el estado de una IP
function verifyRow(button) {
  const row = button.parentNode.parentNode;
  const apiToken = localStorage.getItem("apiToken");
  const ip = row.cells[2].innerText;

  if (!apiToken) {
    alert("Por favor, configure el Token API primero.");
    return;
  }

  fetch(`https://api.hetrixtools.com/v3/blacklist-monitors`, {
    headers: {
      Authorization: `Bearer ${apiToken}`,
    },
  })
    .then((response) => {
      if (response.status === 200) {
        return response.json();
      } else {
        throw new Error("Error al verificar el estado de la IP.");
      }
    })
    .then((data) => {
      const monitor = data.monitors.find((monitor) => monitor.target === ip);
      const status = monitor ? "En lista" : "No en lista";
      const listedIn = monitor ? `${monitor.listed.length} Sitios` : "";
      const updatedAt = convertUTCToLocal(
        new Date().toISOString().slice(0, 19).replace("T", " ")
      );

      row.cells[3].innerText = status;
      row.cells[4].innerText = listedIn;
      row.cells[5].innerText = updatedAt;

      // Enviar los datos actualizados al servidor
      fetch("hetrix.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "update",
          api_key: localStorage.getItem("apiToken"),
          target: ip,
          status: status,
          listed_in: listedIn,
          updated_at: updatedAt,
        }),
      })
        .then((response) => response.text())
        .then((result) => {
          console.log("Update result:", result);
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    })
    .catch((error) => {
      row.cells[3].innerText = "Error";
      row.cells[4].innerText = "Error";
      row.cells[5].innerText = convertUTCToLocal(
        new Date().toISOString().slice(0, 19).replace("T", " ")
      );
      console.error(error.message);
    });
}

// Función para eliminar una IP
function deleteRow(button) {
  const row = button.parentNode.parentNode;
  const ip = row.cells[2].innerText;

  const apiToken = localStorage.getItem("apiToken");

  fetch("hetrix.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      action: "delete",
      api_key: apiToken,
      target: ip,
    }),
  })
    .then((response) => response.text())
    .then((result) => {
      row.parentNode.removeChild(row.nextSibling);
      row.parentNode.removeChild(row);
    })
    .catch((error) => {
      console.error(error.message);
    });
}

// Función para cerrar sesión
function logout() {
  fetch("logout.php")
    .then((response) => {
      if (response.status === 200) {
        window.location.href = "login/login.html";
      } else {
        alert("Error al cerrar sesión.");
      }
    })
    .catch((error) => {
      console.error("Error al cerrar sesión:", error);
    });
}

// Cargar configuraciones y entradas de IP
document.addEventListener("DOMContentLoaded", function () {
  loadApiSettings();
  loadIpEntries();
});

// Función para cargar entradas de IP

function loadIpEntries() {
  fetch("load_ips.php")
    .then((response) => response.json())
    .then((data) => {
      const table = document
        .getElementById("ip-table")
        .getElementsByTagName("tbody")[0];
      data.forEach((entry, index) => {
        const newRow = table.insertRow();
        newRow.className = "main-row";

        newRow.insertCell(0).innerText = index + 1;
        newRow.insertCell(1).innerText = entry.name;
        newRow.insertCell(2).innerText = entry.ip_address;
        newRow.insertCell(3).innerText = "Pendiente";
        newRow.insertCell(4).innerText = "";
        newRow.insertCell(5).innerText = convertUTCToLocal(entry.updated_at); // Conversión de zona horaria
        newRow.insertCell(6).innerHTML = `
            <button class="btn-action" onclick="verifyRow(this)" title="Verificar ahora">
                <img src="https://img.icons8.com/material-outlined/24/000000/user-shield.png"/>
            </button>
            <button class="btn-action" onclick="viewDetails(this)" title="Ver Detalles">
                <img src="https://img.icons8.com/material-outlined/24/000000/eye.png"/>
            </button>
            <button class="btn-action" onclick="deleteRow(this)" title="Eliminar">
                <img src="https://img.icons8.com/material-outlined/24/000000/delete.png"/>
            </button>`;

        const detailRow = table.insertRow();
        detailRow.className = "details-row";
        const detailCell = detailRow.insertCell(0);
        detailCell.colSpan = 7;
        detailCell.innerHTML = `
            <table class="details-table">
                <thead>
                    <tr>
                        <th>RBL</th>
                        <th>URL</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>`;
      });
    })
    .catch((error) => {
      console.error("Error al cargar IPs:", error);
    });
}
