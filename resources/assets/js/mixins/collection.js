export default {
    data() {
        return {
            items: []
        }
    },

    methods: {
        add(item) {
            this.items.push(item);

            this.$emit('added');

            // flash('Your reply has deleted.');
        },

        remove(index) {
            this.items.splice(index, 1);

            this.$emit('removed');

            // flash('Your reply has deleted.');
        }
    }
}