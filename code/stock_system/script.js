    let all=[];

    document.getElementById("addForm").onsubmit=e=>{
    e.preventDefault();
    fetch("add_device.php",{method:"POST",body:new FormData(e.target)})
    .then(()=>{load();e.target.reset();});
    };

    function load(){
    fetch("get_devices.php")
    .then(r=>r.json())
    .then(d=>{all=d;show(d);});
    }

    function show(data){
    let t="";
    data.forEach(d=>{
    let cls="";
    if(d.statut==="Chez le technicien") cls="status-tech";
    else if(d.statut==="Chez le client") cls="status-client";
    else if(d.statut==="En stock") cls="status-stock";

    t+=`
    <tr class="${cls}">
    <td>${d.imei}</td>
    <td>${d.nom_client}</td>
    <td>${d.type_appareil}</td>
    <td>${d.technicien}</td>
    <td><b>${d.statut}</b></td>
    <td>${d.date_ajout}</td>
    <td class="actions">
    <button onclick="setStatut(${d.id},'Chez le technicien')">Technicien</button>
    <button onclick="setStatut(${d.id},'Chez le client')">Client</button>
    <button onclick="setStatut(${d.id},'Installation')">installation</button>
    <button onclick="setStatut(${d.id},'tanger')">tanger</button>
    <button class="btn-danger" onclick="del(${d.id})">Supprimer</button>
    </td>
    </tr>`;
    });
    document.getElementById("data").innerHTML=t;
    }

    function setStatut(id,statut){
    fetch("update_status.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:`id=${id}&statut=${statut}`
    }).then(load);
    }

    function del(id){
    if(confirm("Supprimer cet appareil ?")){
    fetch("delete_device.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:`id=${id}`
    }).then(load);
    }
    }

    function applyFilter(){
    let t=document.getElementById("fTech").value;
    let s=document.getElementById("fStatut").value;
    show(all.filter(d=>
    (t==""||d.technicien==t) &&
    (s==""||d.statut==s)
    ));
    }

    load();
