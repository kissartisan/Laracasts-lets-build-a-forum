<!--
    The parent (Replies.vue) communicate with its child (Reply.vue) component by passing down data
    Replies.vue - data="reply" will cascade down to Reply.vue
    Reply.vue - props: ['data'],
    Reply.vue - body: this.data.body

    The child (Reply.vue) communicate with its parent (Replies.vue) is through events:
    Reply.vue - this.$emit('deleted', this.data.reply_id);
    Replies.vue -  @deleted="remove(index)"
-->

<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.reply_id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <new-reply @created="add"></new-reply>
    </div>
</template>


<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';
    import collection from '../mixins/collection';

    export default {
        components: { Reply, NewReply },

        mixins: [collection],

        data() {
            return { dataSet: false };
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch(page) {
                axios.get(this.url(page))
                     .then(this.refresh);
            },

            url(page) {
                if (! page) {
                    let query = location.search.match(/page=(\d+)/);

                    page = query ? query[1] : 1;
                }

                return `${location.pathname}/replies?page=${page}`;
            },

            refresh({data}) {
                this.dataSet = data;
                this.items = data.data;

                window.scrollTo(0, 0);
            }
        }
    }
</script>