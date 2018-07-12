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
    <div :id="'reply-' + id" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/' + data.owner.name" v-text="data.owner.name"></a>
                    said <span v-text="ago"></span>...
                </h5>

                <div v-if="signedIn">
                    <favorite :reply="data"></favorite>
                </div>
            </div>

        </div>

        <div class="panel-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body" required></textarea>
                    </div>

                    <button class="btn btn-xs btn-primary">Update</button>
                    <button class="btn btn-xs btn-link" @click="editing = false" type="button">Cancel</button>
                </form>
            </div>

            <div v-else v-html="body"></div>
        </div>

        <div class="panel-footer level" v-if="canUpdate">
            <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['data'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.data.reply_id,
                body: this.data.body
            }
        },

        computed: {
            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            },

            signedIn() {
                return window.App.signedIn;
            },

            canUpdate() {
                return this.authorize(user => this.data.user_id == user.user_id);
                // return this.data.user_id == window.App.user.user_id;
            }
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.data.reply_id, {
                    body: this.body
                })
                .catch(error => {
                    flash(error.response.data, 'danger');
                });

                this.editing = false;

                flash('Updated!');
            },

            destroy() {
                axios.delete('/replies/' + this.data.reply_id);

                this.$emit('deleted', this.data.reply_id);
            }
        }
    }
</script>