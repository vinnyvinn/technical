<template>
    <div>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{edit_fuel ? 'Update Fuel' : 'New Fuel'}}</h3>
                </div>
                <div class="box-body">
                    <form @submit.prevent="saveFuel()">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" v-if="company">
                                    <label>Vehicle</label>
                                    <select name="vehicle_id" class="form-control" v-model="form.vehicle_id"
                                            @change="getFuelType()">
                                        <option :value="vehicle.id" v-for="vehicle in vehicles" :key="vehicle.id">
                                            {{vehicle.code}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="other">
                                    <label>Vehicle</label>
                                    <input type="text" v-model="form.vehicle_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Litres</label>
                                    <input type="number" step="0.001" class="form-control" v-model="form.litres"
                                           required>
                                </div>
                                <div class="form-group">
                                    <label>Currency</label>
                                    <select name="currency" class="form-control" v-model="form.currency" required>
                                        <option value="USD">USD</option>
                                        <option value="KSH">KSH</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Invoice No</label>
                                    <input type="text" class="form-control" v-model="form.invoice_no" required>
                                </div>

                                <div class="form-group">
                                    <label>expenses</label>
                                    <select name="expense_id" class="form-control" v-model="form.expense_id">
                                        <option :value="expense.id" v-for="expense in expenses" :key="expense.id">
                                            {{expense.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Misc expenses</label>
                                    <table>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr v-for="(expense,k) in form.other_expenses" :key="k">
                                            <td><input type="text" v-model="expense.name" class="form-control part" placeholder="Name"></td>
                                            <td><input type="number" v-model="expense.qty" class="form-control qty" placeholder="Qty"></td>
                                            <td><input type="number" step="0.001" v-model="expense.amount" class="form-control cost" placeholder="Cost"></td>
                                            <td>

                                                <i class="fa fa-minus-circle remove" @click="removeExpense(k)" v-show="k || ( !k && form.other_expenses.length > 1)"></i>
                                                <i class="fa fa-plus-circle add" @click="addExpense(k)" v-show="k === form.other_expenses.length-1"></i>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" v-if="show_fuel_type">
                                    <br>
                                    <label>Fuel Type: {{fuel_type}}</label>
                                    <label v-if="company" class="pull-right" style="margin-right: 60px;">Previous Odometer Readings: {{previous_odometer}}</label>

                                </div>
                                <div class="form-group" v-if="other">
                                    <label>Fuel Type</label>
                                    <select name="fuel_type_id" v-model="form.fuel_type_id" class="form-control">
                                        <option :value="fuel.id" v-for="fuel in fuel_types" :key="fuel.id">{{fuel.name}}</option>
                                    </select>
                                    </div>
                                <div class="form-group" v-if="company">
                                    <label>Current Odometer Readings</label>
                                    <input type="number" v-model="form.odometer_readings" step="0.001" class="form-control">
                                </div>

                                <div class="form-group" v-if="company">
                                    <label>Driver</label>
                                    <select name="driver_id" class="form-control" v-model="form.driver_id">
                                        <option :value="driver.id" v-for="driver in drivers" :key="driver.id">
                                            {{driver.name}}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select name="supplier_id" class="form-control" v-model="form.supplier_id"
                                            @change="getRate()">
                                        <option :value="supplier.id" v-for="supplier in suppliers" :key="supplier.id">
                                            {{supplier.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="show_rate">
                                    <label>Rate: {{rate}}</label>
                                    <label  class="pull-right" style="margin-right: 60px">Total: {{totalAmount}}</label>
                                </div>
                                <div class="form-group" v-if="other">
                                    <label>Driver</label>
                                    <input type="text" class="form-control" v-model="form.driver_name">
                                </div>
                                <div class="form-group" v-if="other">
                                    <label>Current KM Readings</label>
                                    <input type="number" step="0.001" v-model="form.current_km_readings"
                                           class="form-control" required>
                                </div>
                                         <div class="form-group">
                                        <label>Cash Sale No</label>
                                        <input type="text" class="form-control" v-model="form.cash_sale_no">
                                    </div>
                                        <div class="form-group">
                                        <label>Fuel On</label>
                                        <datepicker v-model="form.fuel_on" required></datepicker>
                                    </div>
                                   </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{edit_fuel ? 'Update' : 'Save'}}</button>
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
        props: ['edit', 'other_fuel', 'add_fuel'],
        data() {
            return {
                form: {
                    litres: '',
                    vehicle_id: '',
                    fuel_on: '',
                    supplier_id: '',
                    invoice_no: '',
                    expense_id: '',
                    fuel_type_id: '',
                    vehicle_name: '',
                    driver_name: '',
                    driver_id: '',
                    current_km_readings: '',
                    currency: '',
                    cash_sale_no: '',
                    odometer_readings:'',
                    other_expenses:[{name:'',qty:'',amount:''}],
                    id: ''
                },
                edit_fuel: this.edit,
                other_fuel_asset: this.other_fuel,
                suppliers: {},
                vehicles: {},
                drivers: {},
                expenses: {},
                company: false,
                other: false,
                fuel_types: {},
                fuel_type: '',
                rate: 0,
                total: 0,
                show_fuel_type: false,
                show_rate: false,
                previous_odometer:0,
            }
        },
        created() {
            this.listen();
            this.getSuppliers();
            this.getVehicles();
            this.getDrivers();
            this.getExpenses();
            this.getFuelTypes();
            this.assetType();

        },
        mounted: function () {

            var self = this;
            $('#datepicker').datepicker({
                onSelect: function (selectedDate, datePicker) {
                    self.form.fuel_on = selectedDate;
                }
            });
        },
        computed: {
            totalAmount() {
                if (this.rate !== 0 && this.form.litres !== '') {
                   return this.total = this.rate * this.form.litres;
                }
            }
        },
          methods: {
            addExpense(){
                this.form.other_expenses.push({name:'',qty:'',amount:''});
            },
              removeExpense(i){
                this.form.other_expenses.splice(i,1);
              },
            getFuelType() {
                this.vehicles.forEach(vehicle => {
                    if (vehicle.id === this.form.vehicle_id) {
                        this.fuel_types.forEach(fuel => {
                            if (fuel.id === vehicle.fuel_type_id) {
                                this.fuel_type = fuel.name;
                                this.form.fuel_type_id = fuel.id;
                                this.show_fuel_type = true;
                                this.previous_odometer = vehicle.odometer_readings;
                            }
                        })
                    }
                })
            },
            getRate() {
                this.suppliers.forEach(supplier => {
                    if (supplier.id === this.form.supplier_id) {
                        this.rate = supplier.price;
                        this.show_rate = true;
                        return;
                    }
                })
            },
            assetType() {
                this.other_fuel_asset ? this.other = true : this.company = true;
            },
            getFuelTypes() {
                axios.get('fuel-types')
                    .then(types => {
                        this.fuel_types = types.data;
                    })
            },
            getSuppliers() {
                axios.get('fuel-supplier')
                    .then(supplier => {
                        this.suppliers = supplier.data;
                    })
            },
            getVehicles() {
                axios.get('machines')
                    .then(vehicle => {
                        this.vehicles = vehicle.data;
                    })
            },
            getDrivers() {
                axios.get('users')
                    .then(driver => {
                        this.drivers = driver.data;
                    })
            },
            getExpenses() {
                axios.get('expense')
                    .then(expense => {
                        this.expenses = expense.data;
                    })
            },
            convertDate(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    day = ("0" + date.getDate()).slice(-2);
                return [date.getFullYear(), mnth, day].join("-");
            },
            saveFuel() {
             if (this.validateExpenses() === false){
                 return this.$toastr.e('All expenses fields are required.')
             }
                if (this.odometerReadings() === false){
                  return this.$toastr.e('Previous Odometer readings cannot be greater than current Odometer readings.');
                }
                this.checkVehicleField();
               this.edit_fuel ? this.update() : this.save();
            },
            save() {
                delete this.form.id;
                this.form.fuel_on = this.convertDate(this.form.fuel_on);
                     axios.post('fuel', this.form)
                    .then(res => eventBus.$emit('listFuels', res.data))
                    .catch(error => error.response)
            },
            update() {
                axios.patch(`fuel/${this.form.id}`, this.form)
                    .then(res => {
                        this.edit_fuel = false;
                        eventBus.$emit('updateFuel', res.data);
                    })
                    .catch(error => error.response)
            },
            cancel() {
                eventBus.$emit('cancel')
            },
              validateExpenses(){
                let dataArray =Object.values(this.form.other_expenses[0]);
                        if (dataArray[0] === null  || dataArray[1] === null || dataArray[2] === null){
                           for (let i = 0; i < this.form.other_expenses.length; i++){
                          if((this.form.other_expenses[i]['name'] ===null || this.form.other_expenses[i]['name'] ==='') || (this.form.other_expenses[i]['qty'] ===null || this.form.other_expenses[i]['qty'] ==='') || (this.form.other_expenses[i]['amount'] ==='null' || this.form.other_expenses[i]['amount'] ==='')){
                          return false;
                             }
                      }
                  }
              },
              odometerReadings(){
                  for (let i=0; i < this.vehicles.length; i++){
                      if (this.vehicles[i]['id'] === this.form.vehicle_id){
                          if (this.vehicles[i]['odometer_readings'] > this.form.odometer_readings){
                              return false;
                             }
                      }
                  }
                  return console.log('oooook');
              },
              checkVehicleField(){
                  if (this.form.vehicle_id ==='' && this.form.vehicle_name ===''){
                      return this.$toastr.e('Please Select a Vehicle.');
                      }
              },
            listen() {
                if (this.edit) {
                    this.form = this.$store.state.fuels;
                    this.form.other_expenses = JSON.parse(this.$store.state.fuels.other_expenses);
                    this.show_fuel_type = true;
                    this.fuel_type = this.$store.state.fuels.fuel_type;
                    this.rate = this.$store.state.fuels.price;
                    this.previous_odometer = this.$store.state.fuels.previous_odometer;
                    this.show_rate = true;
                }
                },
        },
        components: {
            datepicker
        }
    }
</script>

<style scoped>

</style>
