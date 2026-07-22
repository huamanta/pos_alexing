class FluentPaginator {

    constructor({
        url,
        renderTabla,
        paginationId = "#pagination",
        searchSelector = "#search",
        limitSelector = "#limit",
        tableBody = null,
        extraParams = null
    }) {

        this.url = url;
        this.renderTabla = renderTabla;
        this.paginationId = paginationId;
        this.searchSelector = searchSelector;
        this.limitSelector = limitSelector;
        this.tableBody = tableBody;
        this.extraParams = extraParams;

        this.page = 1;
        this.timer = null;

        this.bindEvents();
    }

    showLoading() {

        if (!this.tableBody) return;

        let colspan = $(this.tableBody)
            .closest("table")
            .find("thead th")
            .length;

        $(this.tableBody).html(`
        <tr>
            <td colspan="${colspan}" class="text-center py-5">

                <div class="spinner-border text-primary mb-2"></div>

                <br>

                <strong>Cargando información...</strong>

            </td>
        </tr>
    `);

    }

    showError(message = "Ocurrió un error") {

        if (!this.tableBody) return;

        let colspan = $(this.tableBody)
            .closest("table")
            .find("thead th")
            .length;

        $(this.tableBody).html(`
        <tr>
            <td colspan="${colspan}" class="text-center text-danger py-4">

                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>

                <br>

                ${message}

            </td>
        </tr>
    `);

    }

    bindEvents() {

        $(this.searchSelector).on("keyup", () => {

            clearTimeout(this.timer);

            this.timer = setTimeout(() => {
                this.buscar();
            }, 500);

        });

        $(this.limitSelector).on("change", () => {
            this.buscar();
        });

    }

    buscar() {
        this.load(1);
    }

    async load(page = 1) {

        this.page = page;

        this.showLoading();

        let params = new URLSearchParams({
            page,
            limit: $(this.limitSelector).val() || 10,
            search: $(this.searchSelector).val() || ""
        });

        if (this.extraParams) {

            let extras = typeof this.extraParams === "function"
                ? this.extraParams()
                : this.extraParams;

            Object.entries(extras).forEach(([k, v]) => {
                params.append(k, v);
            });

        }

        try {

            let response = await fetch(
                `${this.url}&${params.toString()}`
            );

            let json = await response.json();

            this.permissions = json.permissions || {};

            this.renderTabla(
                json.data,
                this.permissions
            );

            this.renderPagination(
                json.meta
            );

        } catch (e) {

            console.error(e);

            this.showError(
                "No se pudo cargar la información."
            );

        }

    }

    renderPagination(meta) {

        let current = meta.current_page;
        let last = meta.last_page;

        if (last <= 1) {
            $(this.paginationId).html("");
            return;
        }

        let html = `
            <nav>
                <ul class="pagination justify-content-end">
        `;

        html += `
            <li class="page-item ${current == 1 ? "disabled" : ""}">
                <a class="page-link"
                    href="#"
                    data-page="${current - 1}">
                    Anterior
                </a>
            </li>
        `;

        for (let i = 1; i <= last; i++) {

            html += `
                <li class="page-item ${i == current ? "active" : ""}">
                    <a class="page-link"
                        href="#"
                        data-page="${i}">
                        ${i}
                    </a>
                </li>
            `;

        }

        html += `
            <li class="page-item ${current == last ? "disabled" : ""}">
                <a class="page-link"
                    href="#"
                    data-page="${current + 1}">
                    Siguiente
                </a>
            </li>
        `;

        html += `
                </ul>
            </nav>
        `;

        $(this.paginationId).html(html);

        const self = this;

        $(this.paginationId)
            .find(".page-link")
            .on("click", function (e) {

                e.preventDefault();

                let page = $(this).data("page");

                if (page >= 1 && page <= last) {
                    self.load(page);
                }

            });

    }

}