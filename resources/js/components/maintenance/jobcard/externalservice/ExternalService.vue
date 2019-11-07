<template>
    <div>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{edit_external ? 'Update External Service' : 'New External Service'}}</h3>
                </div>
                <div class="box-body">
                    <form @submit.prevent="saveExternalService()">
                        <div class="form-group">
                            <label>Job Card</label>
                            <select name="job_card_id" class="form-control" v-model="form.job_card_id" required @change="vehicleDetails()">
                                <option :value="job.id" v-for="job in jobs" :key="job.id">{{job.card_no}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>External Service Type</label>
                            <select name="service_type" class="form-control" v-model="form.service_type" required @change="getServiceType()">
                                <option value="parts">Parts</option>
                                <option value="labour">labour</option>
                            </select>
                        </div>
                        <div class="form-group" v-if="show_labour">
                            <label>Service Provider</label>
                            <select name="service_provider_id" class="form-control" v-model="form.service_provider_id">
                                <option :value="p.id" v-for="p in service_providers" :key="p.id">{{p.name}}</option>
                            </select>
                        </div>
                       <div class="row">
                           <div class="col-md-6">
                               <label>Vehicle #:</label> {{vehicle}}
                           </div>
                           <div class="col-md-6">
                               <label>Make & Model:</label> {{make}}
                           </div>
                       </div>
                        <br>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" v-model="form.description" required></textarea>
                        </div>
                        <div class="row" v-if="show_labour">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <datepicker v-model="form.start_date"></datepicker>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <datepicker v-model="form.end_date"></datepicker>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="show_parts">
                            <div class="col-md-3">
                              <div class="form-group">
                                  <label>Item</label>
                                  <input type="text" class="form-control" v-model="form.item">
                              </div>
                            </div>
                            <div class="col-md-3">
                               <div class="form-group">
                                   <label>Supplier</label>
                                   <select name="supplier_id" class="form-control" v-model="form.supplier_id">
                                       <option :value="supplier.id" v-for="supplier in suppliers" :key="supplier.id">{{supplier.name}}</option>
                                   </select>
                               </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cost</label>
                                    <input type="number" step="0.001" class="form-control" v-model="form.cost">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control" v-model="form.quantity">
                                </div>
                            </div>
                        </div>
                         <button type="submit" class="btn btn-primary">{{edit_external ? 'Update' : 'Save'}}</button>
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
        props:['edit'],
        data(){
            return {
                form:{
                    job_card_id:'',
                    start_date:'',
                    end_date:'',
                    description:'',
                    service_type:'',
                    supplier_id:'',
                    service_provider_id:'',
                    item:'',
                    quantity:'',
                    cost:'',
                    id:''
                },
                edit_external: this.edit,
                service_providers:{},
                suppliers:{},
                jobs:{},
                vehicle:'',
                make:'',
                show_parts:false,
                show_labour:false
            }
        },
        created(){
            this.listen();
            this.getSuppliers();
            this.getServiceProviders();
            this.getJobs();
        },
        methods:{
            vehicleDetails(){
              this.jobs.forEach(job => {
                  if (job.id === this.form.job_card_id){
                      this.vehicle = job.machine;
                      this.make = job.make;
                  }
              })
            },
            getServiceType(){
                console.log(this.form.service_type)
               if (this.form.service_type === 'parts'){
                   this.show_parts = true;
                   this.show_labour = false;
               } else{
                   this.show_labour = true;
                   this.show_parts = false;
               }
            },
            getJobs(){
              axios.get('job-card')
                  .then(jobs => {
                      this.jobs = jobs.data;
                  })
            },
            getServiceProviders(){
              axios.get('fuel-supplier')
                  .then(supplier => {
                      this.service_providers = supplier.data
                  })
            },
            getSuppliers(){
              axios.get('service-providers')
                  .then(suppliers => {
                      this.suppliers = suppliers.data;
                  })
            },
            saveExternalService(){

                if (this.form.service_type ==='labour'){
                    if (this.form.start_date ==='' || this.form.end_date ===''){
                        return this.$toastr.e('Start date and End date fields are required.')
                    }
                    else if(moment(this.form.start_date).format('YYY-MM-DD') > moment(this.form.end_date).format('YYY-MM-DD')){
                        return this.$toastr.e('Start date cannot be greater than End date.');
                    }
                    else if(this.form.service_provider_id ===''){
                        return this.$toastr.e('Service provider is required.')
                    }
                }
                else{
                    if (this.form.quantity==='' || this.form.cost==='' || this.form.supplier_id==='' || this.form.item===''){
                        return  this.$toastr.e('All fields are mandatory.');
                    }
                }

                this.edit_external ? this.update() : this.save();
            },
            save(){
                this.form.start_date !=='' ? this.form.start_date = this.convertDate(this.form.start_date) : '';
                this.form.end_date !=='' ? this.form.end_date = this.convertDate(this.form.end_date) : '';
                delete this.form.id;
                axios.post('external-services',this.form)
                    .then(res => eventBus.$emit('listExternalServices',res.data))
                    .catch(error => error.response)
            },
            update(){
                this.form.start_date !==null ? this.form.start_date = this.convertDate(this.form.start_date) : '';
                this.form.end_date !==null ? this.form.end_date = this.convertDate(this.form.end_date) : '';
                  axios.patch(`external-services/${this.form.id}`,this.form)
                    .then(res => {
                        this.edit_external = false;
                        eventBus.$emit('updateExternalService',res.data);
                    })
                    .catch(error => error.response)
            },
                 cancel(){
                eventBus.$emit('cancel')
            },
            convertDate(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    day = ("0" + date.getDate()).slice(-2);
                return [date.getFullYear(), mnth, day].join("-");
            },
            listen(){
                if (this.edit){
                    this.form = this.$store.state.external_services;
                    this.vehicle = this.form.vehicle;
                    this.make = this.form.make;
                    if (this.form.service_type ==='parts'){
                        this.show_parts = true;
                        this.show_labour = false;
                    }else {
                        this.show_labour = true;
                        this.show_parts = false;
                    }
                }
            },
        },
        components:{
            datepicker
        }
    }
</script>

<style scoped>

</style>
