import subprocess
import json
import csv

URL = "http://surco.cidsys.pe/producto/search"

PHPSESSID = "vsj378isbl5310ir775slpl08a"

HEADERS = [
    "-H", "Accept: application/json, text/javascript, */*; q=0.01",
    "-H", "Accept-Language: en-US,en;q=0.9,es;q=0.8",
    "-H", "Connection: keep-alive",
    "-H", "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
    "-H", "Origin: http://surco.cidsys.pe",
    "-H", "Referer: http://surco.cidsys.pe/producto",
    "-H", "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36",
    "-H", "X-Requested-With: XMLHttpRequest",
]


def construir_data(start, length):

    columnas = [
        ("idproducto", "p.idproducto"),
        ("producto", "p.producto"),
        ("marca", "p.marca"),
        ("modelo", "p.modelo"),
        ("placa", "p.placa"),
        ("color", "p.color"),
        ("serie", "p.serie"),
        ("chasis", "p.chasis"),
        ("motor", "p.motor"),
        ("propietario", "p.propietario"),
        ("condicion", "p.condicion"),
        ("precio_venta", "p.precio_venta"),
        ("precio_venta_credito", "p.precio_venta_credito"),
    ]

    partes = [
        "draw=1"
    ]

    for i, (data, name) in enumerate(columnas):

        partes.append(
            f"columns[{i}][data]={data}"
        )

        partes.append(
            f"columns[{i}][name]={name}"
        )

        partes.append(
            f"columns[{i}][searchable]=true"
        )

        partes.append(
            f"columns[{i}][orderable]=true"
        )

        partes.append(
            f"columns[{i}][search][value]="
        )

        partes.append(
            f"columns[{i}][search][regex]=false"
        )

    partes.extend([
        "order[0][column]=0",
        "order[0][dir]=desc",
        f"start={start}",
        f"length={length}",
        "search[value]=",
        "search[regex]=false"
    ])

    return "&".join(partes)


todos = []

start = 0
length = 500

while True:

    print(f"Consultando desde {start}...")

    data = construir_data(start, length)

    comando = [
        "curl",
        "--silent",
        "--show-error",
        "--url", URL,
        *HEADERS,
        "-b", f"PHPSESSID={PHPSESSID}",
        "--data-raw", data,
    ]

    resultado = subprocess.run(
        comando,
        capture_output=True,
        text=True,
        encoding="utf-8",
        errors="replace"
    )

    if resultado.returncode != 0:
        print("ERROR CURL:")
        print(resultado.stderr)
        break

    texto = resultado.stdout.strip()

    try:
        respuesta = json.loads(texto)

    except json.JSONDecodeError:

        print()
        print("======================================")
        print("EL SERVIDOR NO DEVOLVIÓ JSON")
        print("======================================")
        print(texto[:3000])
        print()

        break

    registros = respuesta.get("data", [])

    total = respuesta.get(
        "recordsFiltered",
        respuesta.get("recordsTotal", 0)
    )

    print(
        f"Obtenidos: {len(todos) + len(registros)} / {total}"
    )

    if not registros:
        break

    todos.extend(registros)

    if len(todos) >= total:
        break

    start += length


# ==========================================
# ELIMINAR DUPLICADOS POR ID
# ==========================================

productos = {}

for producto in todos:

    idproducto = producto.get("idproducto")

    if idproducto:
        productos[idproducto] = producto


todos = list(productos.values())


# ==========================================
# CSV
# ==========================================

with open(
    "productos.csv",
    "w",
    newline="",
    encoding="utf-8-sig"
) as archivo:

    writer = csv.writer(archivo)

    writer.writerow([
        "idproducto",
        "producto",
        "marca",
        "modelo",
        "placa",
        "color",
        "serie",
        "chasis",
        "motor",
        "propietario",
        "condicion",
        "precio_venta",
        "precio_venta_credito"
    ])

    for producto in todos:

        writer.writerow([
            producto.get("idproducto", ""),
            producto.get("producto", "").strip(),
            producto.get("marca", "").strip(),
            producto.get("modelo", "").strip(),
            producto.get("placa", "").strip(),
            producto.get("color", "").strip(),
            producto.get("serie", "").strip(),
            producto.get("chasis", "").strip(),
            producto.get("motor", "").strip(),
            producto.get("propietario", "").strip(),
            producto.get("condicion", "").strip(),
            producto.get("precio_venta", ""),
            producto.get("precio_venta_credito", "")
        ])


print()
print("======================================")
print(f"TOTAL EXTRAÍDO: {len(todos)}")
print("ARCHIVO: productos.csv")
print("======================================")