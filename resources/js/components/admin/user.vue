<template>
    <div id="app" class="container">
        <div class="row mt-5" v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users List</h3>

                        <div class="card-tools">
                            <button @click="addModel()" class="btn btn-primary btn-xs pull-right">
                                <i class="fa fa-user-plus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registered_at</th>
                                <th>Action</th>
                            </tr>
                            <tr v-for="(user, index) in users.data" :key="index">
                                <td>{{++index}}</td>
                                <td>{{user.name}}</td>
                                <td>{{user.email}}</td>
                                <td>{{user.type |firstup}}</td>
                                <td>{{user.created_at | mydate}}</td>
                                <td>
                                    <a href="#" class="btn btn-primary" @click="editModel(user)"><i
                                            class="fa fa-edit"></i></a>
                                    <a href="#" class="btn btn-danger" @click="deleteUser(user.id)"><i
                                            class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination  :data="users" v-on:pagination-change-page="getResults"></pagination>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!--Add model starts-->
        <div class="modal fade" tabindex="-1" role="dialog" id="add_model">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 v-show="!editmode" class="modal-title pull-left">Add New User</h4>
                        <h4 v-show="editmode" class="modal-title pull-left">Update User</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="editmode ?updateUser():createUser()">
                        <div class="modal-body">

                            <div class="form-group">
                                <input type="text" name="name" id="name" v-model="form.name" placeholder="Name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" id="email" v-model="form.email"
                                       placeholder="Email Address" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <textarea cols="30" rows="5" name="bio" id="bio" v-model="form.bio"
                                          placeholder="Short bio for user (optional)" class="form-control"
                                          :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                <has-error :form="form" field="bio"></has-error>
                            </div>
                            <div class="form-group">
                                <select id="type" name="type" class="form-control" v-model="form.type"
                                        :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="">Select the type of User</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" id="password" v-model="form.password"
                                       placeholder="Password" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
</template>
<script>

    export default {
        data() {
            return {
                editmode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },
        methods: {
            getResults(page) {
                if (typeof page === 'undefined') {
                    page = 1;
                }

                // Using vue-resource as an example
                axios.get('api/user?page=' + page)
                    .then(response => {
                         this.users = response.data;
                });
            },
            loadUsers() {
                if(this.$gate.isAdminOrAuthor()){
                    axios.get('api/user').then(({data}) => this.users = data);
                }
            },
            addModel() {
                this.editmode = false;
                this.form.clear();
                this.form.reset();
                $("#add_model").modal("show");
            },
            editModel(user) {
                this.editmode = true;
                this.form.clear();
                this.form.reset();
                $("#add_model").modal("show");
                this.form.fill(user);
            },
            createUser() {
                this.$Progress.start();
                this.form.post('api/user')
                    .then(() => {
                        fire.$emit('refresh');
                        $("#add_model").modal("hide");
                        toast({
                            type: 'success',
                            title: 'User Registered Successfully.'
                        })
                        this.$Progress.finish();

                    })
                    .catch(() => {
                        this.$Progress.fail();
                        swal('Oohh!', 'This is a unauthorized action.', 'warning');
                    });
            },
            updateUser() {
                // console.log("updating..");
                this.$Progress.start();
                this.form.put('api/user/' + this.form.id)
                    .then(() => {
                        fire.$emit('refresh');
                        $("#add_model").modal("hide");
                        swal(
                            'Updated!',
                            'User information has been updated.',
                            'success'
                        )
                        this.$Progress.finish();
                    }).catch(() => {
                    this.$Progress.fail();
                    swal('Oohh!', 'This is a unauthorized action.', 'warning');
                });
            },
            deleteUser(id) {
                // alert(id);
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        //Delete request to server.
                        this.form.delete('api/user/' + id)
                            .then(() => {
                                swal(
                                    'Deleted!',
                                    'User has been deleted.',
                                    'success'
                                )
                              fire.$emit('refresh');

                            }).catch(() => {
                            swal('Oohh!', 'This is a unauthorized action.', 'warning');
                        });
                    }
                })
            }
        },
        created() {
            this.loadUsers();
            fire.$on('refresh', () => {
                this.loadUsers();
            });
        }
    }
</script>
<style></style>