export default function postTestQuestionPicker({ postTestId, usedIds = [] }) {
    return {
        categoryId: '',
        materialId: '',
        type: '',
        materials: [],
        questions: [],
        selected: [],
        pagination: {
            current_page: 1,
            last_page: 1,
        },

        async loadMaterials() {
            this.materialId = ''
            this.materials = []
            this.questions = []

            const res = await fetch(`/ajax/categories/${this.categoryId}/materials`)
            if (res.headers.get('content-type')?.includes('text/html')) {
                console.error('Response bukan JSON (loadMaterials)')
                return
            }
            this.materials = await res.json()
        },

        async fetchQuestions(page = 1) {
            if (!this.materialId) return

            let url = `/ajax/post-tests/${postTestId}/questions/by-material/${this.materialId}?page=${page}`

            if (this.type) url += `&type=${this.type}`

            const res = await fetch(url)
            if (res.headers.get('content-type')?.includes('text/html')) {
                console.error('Response bukan JSON (fetchQuestions)')
                return
            }
            const data = await res.json()

            this.questions = data.data.map(q => ({
                ...q,
                image_url: q.image
                    ? `/storage/${q.image}`
                    : null
            }))

            this.pagination = data

            this.$nextTick(() => {
                if (window.MathJax) MathJax.typesetPromise()
            })
        }
    }
}
