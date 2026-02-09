/**
 * ========================================
 * AJAX HANDLER - Sistema de peticiones AJAX
 * ========================================
 * 
 * Uso básico:
 *   Ajax.get('/api/usuarios')
 *       .then(data => console.log(data))
 *       .catch(error => console.error(error));
 */

class Ajax {
    /**
     * Configuración por defecto
     */
    static config = {
        baseUrl: '', // URL base de la API
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest' // Identificar como AJAX
        },
        timeout: 30000, // 30 segundos
        beforeSend: null, // Callback antes de enviar
        afterSend: null,  // Callback después de enviar
    };

    /**
     * GET - Obtener datos
     * 
     * Uso: Ajax.get('/usuarios/5')
     */
    static async get(url, params = {}) {
        // Construir query string si hay parámetros
        if (Object.keys(params).length > 0) {
            const queryString = new URLSearchParams(params).toString();
            url = url + '?' + queryString;
        }

        return this.request(url, {
            method: 'GET'
        });
    }

    /**
     * POST - Enviar datos
     * 
     * Uso: Ajax.post('/usuarios/guardar', { nombre: 'Juan', email: 'juan@mail.com' })
     */
    static async post(url, data = {}) {
        return this.request(url, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    /**
     * PUT - Actualizar datos
     * 
     * Uso: Ajax.put('/usuarios/5', { nombre: 'Juan Actualizado' })
     */
    static async put(url, data = {}) {
        return this.request(url, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    /**
     * DELETE - Eliminar datos
     * 
     * Uso: Ajax.delete('/usuarios/5')
     */
    static async delete(url) {
        return this.request(url, {
            method: 'DELETE'
        });
    }

    /**
     * FORM - Enviar formulario (con archivos)
     * 
     * Uso: 
     *   const formData = new FormData(document.querySelector('#miForm'));
     *   Ajax.form('/usuarios/guardar', formData)
     */
    static async form(url, formData) {
        // Para FormData NO enviamos Content-Type, el navegador lo hace automáticamente
        const headers = { ...this.config.headers };
        delete headers['Content-Type'];

        return this.request(url, {
            method: 'POST',
            body: formData,
            headers: headers
        });
    }

    /**
     * REQUEST - Método principal que hace la magia 
     */
    static async request(url, options = {}) {
        // Añadir URL base si existe
        const fullUrl = this.config.baseUrl + url;

        // Preparar opciones
        const fetchOptions = {
            method: options.method || 'GET',
            headers: options.headers || this.config.headers,
            ...options
        };

        // Ejecutar beforeSend si existe
        if (this.config.beforeSend) {
            this.config.beforeSend(fullUrl, fetchOptions);
        }

        try {
            // Crear timeout promise
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Timeout: La petición tardó demasiado')),
                    this.config.timeout);
            });

            // Hacer la petición con timeout
            const response = await Promise.race([
                fetch(fullUrl, fetchOptions),
                timeoutPromise
            ]);

            // Ejecutar afterSend si existe
            if (this.config.afterSend) {
                this.config.afterSend(response);
            }

            // Verificar si la respuesta es OK
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            // Intentar parsear como JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }

            // Si no es JSON, retornar como texto
            return await response.text();

        } catch (error) {
            console.error('Error en petición AJAX:', error);
            throw error;
        }
    }

    /**
     * CONFIGURE - Configurar AJAX globalmente
     * 
     * Uso: 
     *   Ajax.configure({
     *       baseUrl: '/api',
     *       beforeSend: () => mostrarLoader(),
     *       afterSend: () => ocultarLoader()
     *   })
     */
    static configure(options) {
        this.config = { ...this.config, ...options };
    }
}

/**
 * ========================================
 * FORM HANDLER - Manejo de formularios AJAX
 * ========================================
 * 
 * Hace que tus formularios envíen datos por AJAX automáticamente
 * sin necesidad de programar nada especial.
 * 
 */

class AjaxForm {
    constructor(formElement) {
        if (formElement.dataset.ajaxInitialized) return;
        formElement.dataset.ajaxInitialized = 'true';

        this.form = formElement;
        this.submitButton = this.form.querySelector('[type="submit"]');
        this.originalButtonText = this.submitButton ? this.submitButton.innerHTML : '';

        // Configurar eventos
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    /**
     * Manejar envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();

        // Validar formulario HTML5
        /* if (!this.form.checkValidity()) {
            this.form.reportValidity();
            return;
        } */

        // Obtener datos del formulario
        const formData = new FormData(this.form);
        const url = this.form.action;
        const method = this.form.method.toUpperCase();

        // Deshabilitar botón y mostrar loading
        this.setLoading(true);

        try {
            let response;

            // Verificar si hay archivos
            const hasFiles = Array.from(formData.values()).some(value => value instanceof File);

            if (hasFiles) {
                // Si hay archivos, enviar como FormData
                response = await Ajax.form(url, formData);
            } else {
                // Si no hay archivos, convertir a JSON
                const data = Object.fromEntries(formData);

                if (method === 'POST') {
                    response = await Ajax.post(url, data);
                } else if (method === 'PUT') {
                    response = await Ajax.put(url, data);
                }
            }

            // Ejecutar callback de éxito si existe
            if (this.onSuccess) {
                this.onSuccess(response);
            } else {
                this.defaultSuccess(response);
            }

        } catch (error) {
            // Ejecutar callback de error si existe
            if (this.onError) {
                this.onError(error);
            } else {
                this.defaultError(error);
            }
        } finally {
            // Rehabilitar botón
            this.setLoading(false);
        }
    }

    /**
     * Manejo de éxito por defecto
     */
    defaultSuccess(response) {
        // Si la respuesta tiene un mensaje, mostrarlo
        if (response.message) {
            alert(response.message);
        }

        // Si la respuesta tiene una URL de redirección, redirigir
        if (response.redirect) {
            window.location.href = response.redirect;
        }

        // Limpiar formulario si fue exitoso
        if (response.success) {
            this.form.reset();
        }
    }

    /**
     * Manejo de error por defecto
     */
    defaultError(error) {
        //alert('Error: ' + error.message);
    }

    /**
     * Mostrar/ocultar estado de carga
     */
    setLoading(isLoading) {
        if (!this.submitButton) return;

        if (isLoading) {
            this.submitButton.disabled = true;
            this.submitButton.innerHTML = '<span class="spinner"></span> Enviando...';
        } else {
            this.submitButton.disabled = false;
            this.submitButton.innerHTML = this.originalButtonText;
        }
    }

    /**
     * Configurar callback de éxito personalizado
     */
    success(callback) {
        this.onSuccess = callback;
        return this;
    }

    /**
     * Configurar callback de error personalizado
     */
    error(callback) {
        this.onError = callback;
        return this;
    }
}

/**
 * ========================================
 * AUTO-INICIALIZACIÓN
 * ========================================
 * 
 * Busca automáticamente formularios con data-ajax-form
 * y los convierte en formularios AJAX
 */

document.addEventListener('DOMContentLoaded', () => {
    // Auto-inicializar formularios con data-ajax-form
    document.querySelectorAll('[data-ajax-form]').forEach(form => {
        new AjaxForm(form);
    });
});

/**
 * ========================================
 * UTILIDADES AJAX
 * ========================================
 */

const AjaxUtils = {
    /**
     * Eliminar elemento con confirmación
     * 
     * Uso: <button onclick="AjaxUtils.confirmDelete('/usuarios/5', this)">Eliminar</button>
     */
    confirmDelete(url, element, confirmMessage = '¿Estás seguro de eliminar este registro?') {
        if (!confirm(confirmMessage)) {
            return;
        }

        // Obtener fila de tabla si existe
        const row = element.closest('tr');

        Ajax.delete(url)
            .then(response => {
                if (response.success) {
                    // Animar y remover fila
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transition = 'opacity 0.3s';
                        setTimeout(() => row.remove(), 300);
                    }

                    alert(response.message || 'Eliminado correctamente');
                } else {
                    alert(response.message || 'Error al eliminar');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
    },

    /**
     * Cargar contenido en un elemento
     * 
     * Uso: AjaxUtils.loadContent('/usuarios/lista', '#contenedor')
     */
    loadContent(url, selector) {
        const container = document.querySelector(selector);

        if (!container) {
            console.error('Contenedor no encontrado:', selector);
            return;
        }

        // Mostrar loader
        container.innerHTML = '<div class="loader">Cargando...</div>';

        Ajax.get(url)
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => {
                container.innerHTML = `<div class="error">Error al cargar: ${error.message}</div>`;
            });
    },

    /**
     * Refrescar tabla con datos
     * 
     * Uso: AjaxUtils.refreshTable('/api/usuarios', '#tabla-usuarios tbody')
     */
    refreshTable(url, selector) {
        Ajax.get(url)
            .then(data => {
                const tbody = document.querySelector(selector);

                if (!tbody) {
                    console.error('Tabla no encontrada:', selector);
                    return;
                }

                // Aquí deberías tener una función para renderizar las filas
                // Ejemplo básico:
                tbody.innerHTML = data.map(item => `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.nombre}</td>
                        <!-- Más columnas según tus datos -->
                    </tr>
                `).join('');
            })
            .catch(error => {
                console.error('Error al refrescar tabla:', error);
            });
    }
};

// Exportar para uso global
window.Ajax = Ajax;
window.AjaxForm = AjaxForm;
window.AjaxUtils = AjaxUtils;