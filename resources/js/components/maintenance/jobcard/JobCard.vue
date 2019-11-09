<template>
    <div>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{edit_jobcard ? 'Update Jobcard' : 'New Jobcard'}}</h3>
                </div>
                <div class="box-body">
                    <form @submit.prevent="saveJobcard()" enctype="multipart/form-data" id="jobcard">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="radio-inline"><input type="radio" name="service_type" value="repair"
                                                                       v-model="service_type">Repairs</label>
                                    <label class="radio-inline"><input type="radio" name="service_type"
                                                                      value="maintenance" v-model="service_type">Maintenance</label>
                                </div>
                                <div class="form-group">
                                    <label>Vehicle/Chasis #</label>
                                    <select name="machine_id" class="form-control" v-model="form.machine_id"
                                            @change="getAssetDetails()" required>
                                        <option :value="machine.id" v-for="machine in machines" :key="machine.id">
                                            {{machine.code}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Track By:</label> {{track_name}}

                                </div>
                                <div class="form-group">
                                    <label>Service Provider Type</label>
                                    <select name="service_provider_type_id" class="form-control"
                                            v-model="form.service_provider_type_id" @change="getType()" required>
                                        <option :value="providertype.id" v-for="providertype in service_provider_types"
                                                :key="providertype.id">{{providertype.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="external">
                                    <label>Service Provider</label>
                                    <select name="service_provider_id" class="form-control"
                                            v-model="form.service_provider_id">
                                        <option :value="supplier.id" v-for="supplier in suppliers" :key="supplier.id">
                                            {{supplier.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="internal">
                                    <label>Service Provider</label>
                                    <select name="supplier_id" class="form-control" v-model="form.supplier_id">
                                        <option :value="s.id" v-for="s in internal_suppliers" :key="s.id">{{s.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Expenses</label>
                                    <table>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr v-for="(item,k) in form.item_cost_qty" :key="k">
                                            <td><select class="form-control" v-model="item.part">
                                                <option selected disabled>Select Part</option>
                                                <option :value="part.id" v-for="part in parts" :key="part.id">
                                                    {{part.code}} - {{part.description}}
                                                </option>
                                            </select>
                                            </td>
                                            <td><input type="number" class="form-control qty" v-model="item.quantity"
                                                       placeholder="Qty">
                                            </td>
                                            <td>
                                                <i class="fa fa-minus-circle remove" @click="removeparts(k)"
                                                   v-show="k || ( !k && form.item_cost_qty.length > 1)"></i>
                                                <i class="fa fa-plus-circle add" @click="addparts(k)"
                                                   v-show="k == form.item_cost_qty.length-1"></i>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Labour</label>
                                            <input type="text" class="form-control" v-model="form.labour">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Labour Cost</label>
                                            <input type="number" step="0.001" class="form-control" v-model="form.cost">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group" v-if="!serviceType">
                                    <label>Service Type</label>
                                    <select name="service_type_id" class="form-control" v-model="form.service_type_id"
                                            @change="nextReadings()">
                                        <option :value="service.id" v-for="service in service_types" :key="service.id">
                                            {{service.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="serviceType">
                                    <label>Service Description</label>
                                    <input type="text" class="form-control" v-model="form.service_type_description">
                                </div>

                                <div class="form-group">
                                    <label>Actual Date</label>
                                    <datepicker v-model="form.actual_date" required></datepicker>

                                </div>
                                <div class="form-group">
                                    <label>Time In</label>
                                    <input type="text" v-model="form.time_in" class="form-control time_in" required>

                                </div>
                                <div class="form-group">
                                    <label>Actual Completion Date</label>
                                    <datepicker v-model="form.completion_date" ref="completionDate"
                                                required></datepicker>
                                </div>
                                <div class="form-group">
                                    <label>Time Out</label>
                                    <input type="text" v-model="form.time_out" class="form-control time_out"
                                           ref="timeOut"
                                           required>
                                </div>
                                <div class="form-group">
                                    <label>Upload Checklist</label>
                                    <input type="file" class="form-control" @change="onFileChange">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Make & Model: </label> {{make}}

                                </div>
                                <div class="form-group">
                                    <label>Assigned To: </label> {{driver}}
                                </div>
                                <div class="form-group">
                                    <label>Fuel Balance</label>
                                    <select name="fuel_balance_id" class="form-control" v-model="form.fuel_balance_id"
                                            required>
                                        <option :value="bal.id" v-for="bal in balances" :key="bal.id">{{bal.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="show_track_by">
                                    <label class="pull-left">Previous {{track_name}} Reading:
                                        {{previous_readings}}</label>
                                    <br><br>
                                    <label>Current {{track_name}} Readings</label>

                                    <input type="number" step="0.001" class="form-control"
                                           v-model="form.current_readings" required>
                                    <br>
                                    <label>Actual {{track_name}} Covered: {{actualMileage ? actualMileage : 0}}</label>
                                </div>
                                <div class="form-group" v-if="show_track_by">
                                    <label>Next {{track_name}} Maintenance</label>
                                    <input type="number" step="0.001" class="form-control" v-model="form.next_readings"
                                           required :disabled="!show_next_readings">
                                </div>
                                <div class="form-group">
                                    <label>Next Service Date</label>
                                    <datepicker v-model="form.next_service_date" ref="nextServiceDate"
                                                required></datepicker>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Maintenance</label>
                                    <table style="width:100%">
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr v-for="(m,i) in form.maintenance">
                                            <td><select class="form-control" v-model="m.category"
                                                        placeholder="Category">
                                                <option selected disabled>Select Category</option>
                                                <option :value="cat.id" v-for="cat in categories" :key="cat.id">
                                                    {{cat.name}}
                                                </option>
                                            </select></td>

                                            <td><textarea class="form-control cost" v-model="m.description"
                                                          placeholder="Description"></textarea></td>
                                            <td><textarea class="form-control cause" v-model="m.root_cause"
                                                          placeholder="Root cause"></textarea></td>
                                            <td>
                                                <i class="fa fa-minus-circle remove" @click="removeItem(i)"
                                                   v-show="i || (!i && form.maintenance.length > 1)"></i>
                                                <i class="fa fa-plus-circle add" @click="addItem(i)"
                                                   v-show="i == form.maintenance.length -1"></i>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{edit_jobcard ? 'Update' : 'Save'}}</button>
                        <button type="button" class="btn btn-outline-danger" @click="cancel">Cancel</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
    import datepicker from 'vuejs-datepicker';
    export default {
        props: ['edit'],
        data() {
            return {
                form: {
                    machine_id: '',
                    track_by_id: '',
                    service_provider_type_id: '',
                    service_provider_id: '',
                    service_type_id: '',
                    actual_date: '',
                    completion_date: '',
                    description: '',
                    file: '',
                    time_in: '',
                    time_out: '',
                    fuel_balance_id: '',
                    next_readings: 0,
                    next_service_date: '',
                    supplier_id: '',
                    current_readings: '',
                    service_type_description: '',
                    labour: '',
                    cost: '',
                    id: '',
                    item_cost_qty: [
                        {part: '', quantity: ''}
                    ],
                    maintenance: [{category: '', description: '', root_cause: ''}]
                },
                checklist: '',
                edit_jobcard: this.edit,
                machines: {},
                tracks: {},
                service_provider_types: {},
                suppliers: {},
                service_types: {},
                services: {},
                balances: {},
                internal: false,
                external: false,
                track_name: '',
                show_track_by: false,
                make: '',
                driver: '',
                users: {},
                previous_readings: 0,
                actual_readings: 0,
                service_after: 0,
                service_type: 'repair',
                internal_suppliers: {},
                next_computed_readings: 0,
                show_next_readings: true,
                parts: {},
                categories: {}

            }
        },
        watch: {
            'form.current_readings'() {
                if (this.service_type === 'maintenance' && this.service_after > 0) {
                    this.form.next_readings = parseFloat(this.form.current_readings) + parseFloat(this.service_after);
                    this.show_next_readings = false;
                } else {
                    this.show_next_readings = true;
                }
            },
            timeframe() {
                if (this.form.actual_date !== '' && this.form.completion_date !== '') {
                    if (moment(this.form.actual_date).format('YYYY-MM-DD') > moment(this.form.completion_date).format('YYYY-MM-DD')) {
                        this.$refs.completionDate.clearDate();
                        this.form.completion_date = '';
                        return this.$toastr.e('Sorry, actual date cannot be greater than completion date.');
                    }
                }
                if (moment(this.form.next_service_date).format('YYYY-MM-DD') < moment(Date.now()).format('YYYY-MM-DD')) {
                    this.$refs.nextServiceDate.clearDate();
                    this.form.next_service_date = '';
                    return this.$toastr.e('Sorry, next service date cannot be later than today.');
                }
                if (this.form.actual_date !== '' && this.form.completion_date !== '' && this.form.time_in !== '' || this.form.time_out !== '') {
                    if (moment(this.form.actual_date).format('YYYY-MM-DD') === moment(this.form.completion_date).format('YYYY-MM-DD')) {
                        if (moment(this.form.time_in, 'h:mm A').format('HH:mm') > moment(this.form.time_out, 'h:mm A').format('HH:mm')) {
                            this.form.time_out = '';
                            return this.$toastr.e('Sorry, time in cannot be greater than time out.');
                        }
                    }
                }
            }
        },
        mounted() {
            this.initDate();
        },
        created() {
            this.listen();
            this.getMachines();
            this.getUsers();
            this.getTracks();
            this.getProviderTypes();
            this.getSuppliers();
            this.getInternalSuppliers();
            this.getServiceTypes();
            this.getBalances();
            this.getParts();
            this.getCategories();

        },
        filters: {
            moment: function (date) {
                return moment(date);
            }
        },
        computed: {
            actualMileage() {
                if (this.form.current_readings > this.previous_readings) {
                    return this.form.current_readings - this.previous_readings;
                }
            },
            serviceType() {
                return this.service_type === 'repair';
            },
            timeframe() {
                return [this.form.actual_date, this.form.time_in, this.form.completion_date, this.form.time_out, this.form.next_service_date].join();
            }
        },
        methods: {
            addItem(i) {
                this.form.maintenance.push({category: '', description: '', root_cause: ''});
            },
            removeItem(i) {
                this.form.maintenance.splice(i, 1);
            },
            getCategories() {
                axios.get('categories')
                    .then(category => {
                        this.categories = category.data
                    })
            },
            getParts() {
                axios.get('parts')
                    .then(res => {
                        this.parts = res.data
                    })
            },
            nextReadings() {
                if (this.service_type === 'maintenance') {
                    this.services.forEach(service => {
                        if (service.id === this.form.service_type_id) {
                            this.service_after = service.service_after;
                        }
                    })
                } else {
                    this.form.service_type_id === '';
                    this.service_after = 0;
                }
            },
            initDate() {
                var vm = this;
                $('.time_in').timepicker({'scrollDefault': 'now'}).on('changeTime', function () {
                    vm.form.time_in = $('.time_in').val();
                })
                $('.time_out').timepicker({'scrollDefault': 'now'}).on('changeTime', function () {
                    vm.form.time_out = $('.time_out').val();
                })
            },
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.form.file = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            addparts() {
                this.form.item_cost_qty.push({part: '', quantity: ''});
            },
            removeparts(i) {
                this.form.item_cost_qty.splice(i, 1);
            },
            getType() {
                this.service_provider_types.forEach(type => {
                    if (type.id === this.form.service_provider_type_id) {
                        if (type.name === 'Internal') {
                            this.internal = true;
                            this.external = false;
                        } else {
                            this.external = true;
                            this.internal = false;
                        }
                    }
                })
            },
            getAssetDetails() {
                this.machines.forEach(machine => {
                    if (machine.id === this.form.machine_id) {
                        this.make = machine.make;
                        this.previous_readings = machine.current_readings;
                        this.form.track_by_id = machine.track_by_id;
                        this.tracks.forEach(track => {
                            if (track.id === machine.track_by_id) {
                                this.track_name = track.name;
                                this.show_track_by = true;
                            }
                        });
                        this.users.forEach(user => {
                            if (user.id === machine.assign_to) {
                                this.driver = user.name;
                                return;
                            }
                        })
                        this.service_types = machine.service_types
                    }
                    return;
                })
            },
            saveJobcard() {
                this.machines.forEach(machine => {
                    if (machine.id === this.form.machine_id) {
                        if (machine.next_readings > this.form.next_readings) {
                            return this.$toastr.e(`Sorry, Next ${this.track_name} readings cannot be less than previous readings.`);
                        }
                        if (machine.current_readings > this.form.current_readings) {
                            return this.$toastr.e(`Sorry, Current ${this.track_name} readings cannot be less than previous readings.`);
                        }
                    }
                });

                if (parseFloat(this.form.current_readings) > parseFloat(this.form.next_readings)) {
                    return this.$toastr.e(`Sorry, Current ${this.track_name} readings cannot be greater than next readings.`);
                }
                if (Object.values(this.form.item_cost_qty[0])[0] !== '' || Object.values(this.form.item_cost_qty[0])[1] !== '') {
                    for (let i = 0; i < this.form.item_cost_qty.length; i++) {
                        if (this.form.item_cost_qty[i]['part'] === '' || this.form.item_cost_qty[i]['quantity'] === '') {
                            return this.$toastr.e('Please all expenses fields are required.');
                        }
                    }
                }
                if (Object.values(this.form.maintenance[0])[0] !== '' || Object.values(this.form.maintenance[0])[1] !== '' || Object.values(this.form.maintenance[0])[2] !== '') {
                    for (let i = 0; i < this.form.maintenance.length; i++) {
                        if (this.form.maintenance[i]['category'] === '' || this.form.maintenance[i]['description'] === '' || this.form.maintenance[i]['root_cause'] === '') {
                            return this.$toastr.e('Please all Maintenance fields are required.');
                        }
                    }
                }
                if (this.form.service_provider_id === '' && this.form.supplier_id === '') {
                    return this.$toastr.e('Service provider field is required');
                }
                if (this.form.time_out === '') {
                    return this.$toastr.e('Time Out field is required');
                }
                if (this.form.next_service_date === '' || this.form.completion_date === '') {
                    return this.$toastr.e('Actual completion and Next service dates are required.');
                }
                if (this.form.service_type_description === '' && this.form.service_type_id === '') {
                    return this.$toastr.e('Service type/description is required.');
                }
                this.edit_jobcard ? this.update() : this.save();

            },
            save() {
                let form = document.getElementById('jobcard');
                let formData = new FormData(form);

                formData.append('checklist_file', this.form.file);
                formData.append('time_in', this.form.time_in);
                formData.append('track_by_id', this.form.track_by_id);
                formData.append('time_out', this.form.time_out);
                formData.append('current_readings', this.form.current_readings);
                formData.append('next_readings', this.form.next_readings);
                formData.append('next_service_date', this.convertDate(this.form.next_service_date));
                formData.append('actual_date', this.convertDate(this.form.actual_date));
                formData.append('service_provider_id', this.form.service_provider_id);
                formData.append('supplier_id', this.form.supplier_id);
                formData.append('completion_date', this.convertDate(this.form.completion_date));
                formData.append('service_type_description', this.form.service_type_description);
                formData.append('labour', this.form.labour);
                formData.append('cost', this.form.cost);
                formData.append('service_type', this.service_type);
                formData.append('fuel_balance_id', this.form.fuel_balance_id);
                formData.append('item_cost_qty', JSON.stringify(this.form.item_cost_qty));
                formData.append('maintenance', JSON.stringify(this.form.maintenance));
                const config = {
                    headers: {'Content-Type': 'multipart/form-data'}
                }
                delete this.form.id;
                axios.post('job-card', formData, config).then(res => {
                    //console.log(res.data);
                    eventBus.$emit('listJobcards', res.data)
                })
                    .catch(error => error.response)
            },
            convertDate(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    day = ("0" + date.getDate()).slice(-2);
                return [date.getFullYear(), mnth, day].join("-");
            },
            update() {
                let form = document.getElementById('jobcard');
                let formData = new FormData(form);

                formData.append('checklist_file', this.form.file);
                formData.append('time_in', this.form.time_in);
                formData.append('track_by_id', this.form.track_by_id);
                formData.append('id', this.form.id);
                formData.append('time_out', this.form.time_out);
                formData.append('current_readings', this.form.current_readings);
                formData.append('next_readings', this.form.next_readings);
                formData.append('next_service_date', this.convertDate(this.form.next_service_date));
                formData.append('actual_date', this.convertDate(this.form.actual_date));
                formData.append('service_provider_id', this.form.service_provider_id);
                formData.append('supplier_id', this.form.supplier_id);
                formData.append('completion_date', this.convertDate(this.form.completion_date));
                formData.append('service_type_description', this.form.service_type_description);
                formData.append('labour', this.form.labour);
                formData.append('cost', this.form.cost);
                formData.append('service_type', this.service_type);
                formData.append('fuel_balance_id', this.form.fuel_balance_id);
                formData.append('item_cost_qty', JSON.stringify(this.form.item_cost_qty));
                formData.append('maintenance', JSON.stringify(this.form.maintenance));
                formData.append('_method', 'PUT');
                const config = {
                    headers: {'Content-Type': 'multipart/form-data'}
                }

                axios.post(`job-card/${this.form.id}`, formData, config).then(res => {
                    eventBus.$emit('updateJobcard', res.data)
                })
                    .catch(error => error.response)

            },
            getMachines() {
                axios.get('machines')
                    .then(machines => {
                        this.machines = machines.data;
                    })
            },
            getTracks() {
                axios.get('track-by')
                    .then(tracks => {
                        this.tracks = tracks.data;
                    })
            },
            getProviderTypes() {
                axios.get('service-provider-types')
                    .then(providers => {
                        this.service_provider_types = providers.data
                    })
            },
            getSuppliers() {
                axios.get('service-providers')
                    .then(suppliers => {
                        this.suppliers = suppliers.data;
                    })
            },
            getInternalSuppliers() {
                axios.get('fuel-supplier')
                    .then(supplier => {
                        this.internal_suppliers = supplier.data;
                    })
            },
            getServiceTypes() {
                axios.get('service-types')
                    .then(service_types => {
                        this.services = service_types.data;
                    })
            },
            getBalances() {
                axios.get('fuel-balance')
                    .then(balance => {
                        this.balances = balance.data;
                    })
            },
            getUsers() {
                axios.get('users')
                    .then(users => {
                        this.users = users.data;
                    })
            },
            cancel() {
                eventBus.$emit('cancel')
            },
            listen() {
                if (this.edit) {
                    this.form = this.$store.state.job_card
                    this.service_type = this.$store.state.job_card.service_type;
                    this.show_track_by = true;
                    this.track_name = this.$store.state.job_card.track_name;
                    this.previous_readings = this.$store.state.job_card.current_readings;
                    this.make = this.$store.state.job_card.make;
                    this.driver = this.$store.state.job_card.driver;
                    this.service_types = this.form.service_types;
                    this.form.item_cost_qty = JSON.parse(this.$store.state.job_card.item_cost_qty);
                    this.form.maintenance = JSON.parse(this.$store.state.job_card.maintenance);
                    if (this.$store.state.job_card.service_provider_id === null || this.$store.state.job_card.service_provider_id === 'null') {
                        this.internal = true;
                    } else {
                        this.external = true;
                    }
                    console.log(this.form)
                }
            },
        },
        components: {
            datepicker
        }
    }
</script>

<style>
    .part {
        margin-bottom: 5px
    }

    .qty, .cost {
        margin-bottom: 5px;
        margin-left: 5px
    }

    .cost {
        margin-bottom: 5px;
        margin-left: 10px
    }

    .remove, .add {
        margin-left: 20px
    }

    .cause {
        margin-left: 15px;
        margin-bottom: 10px;
    }

    .vdp-datepicker input {
        border-radius: 0;
        box-shadow: none;
        border-color: #d2d6de;
        display: block;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    }

</style>
