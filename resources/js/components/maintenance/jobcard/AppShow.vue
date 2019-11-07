<template>
<div>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
              <div class="box-body" id="printMe">
                <div class="invoice-box" style="max-width: 800px;margin: auto;padding: 30px;border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);font-size: 16px;line-height: 24px;color: #555;">
                    <table cellpadding="0" cellspacing="0">
                        <tr class="top">
                            <td colspan="4" style="padding: 5px;vertical-align: top;">
                                <table style="width: 100%;line-height: inherit;text-align: left;">
                                    <tr>
                                        <td class="title">
                                            <img src="https://www.sparksuite.com/images/logo.png" style="width:100%; max-width:300px;">
                                        </td>

                                        <td>
                                            Invoice #: 123<br> Created: January 1, 2015<br> Due: February 1, 2015
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr class="information">
                            <td colspan="4">
                                <table>
                                    <tr>
                                        <td style="padding-bottom: 40px;">
                                            Sparksuite, Inc.<br> 12345 Sunny Road<br> Sunnyville, CA 12345
                                        </td>

                                        <td style="padding-bottom: 40px;">
                                            Acme Corp.<br> John Doe<br> john@example.com
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr class="heading">
                            <td colspan="3">Payment Method</td>
                            <td>Check #</td>
                        </tr>

                        <tr class="details">
                            <td colspan="3" style="padding-bottom: 20px;">Check</td>
                            <td style="padding-bottom: 20px;">1000</td>
                        </tr>

                        <tr class="heading">
                            <td style="background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">Item</td>
                            <td style="background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">Unit Cost</td>
                            <td style="background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">Quantity</td>
                            <td style="background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">Price</td>
                        </tr>

                        <tr>
                            <td>Maize</td>
                            <td>$5000</td>
                            <td>20</td>
                            <td>$50000</td>
                        </tr>

                        <tr class="total">
                            <td colspan="3"></td>
                            <td>Total: $20000</td>
                        </tr>
                    </table>

                </div>

            </div>
            <button class="btn btn-success" @click="printInvoice" id="print">Print Job Card</button>
        </div>
    </section>
</div>
</template>

<script>
    export default {
        data(){
            return {
                job:{},
            }
        },
        created(){
          this.getJob();
        },
        methods:{
            getJob(){
                axios.get('job-card/'+this.$route.params['id'])
                    .then(job => {
                        this.job = job.data
                        console.log(this.job)
                    })

            },
            printInvoice: function() {
                this.$htmlToPaper('printMe');
            },
        }
    }
</script>

<style scoped>
       .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 16px;
        line-height: 24px;
        font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
        color: #555;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(n + 2) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td {
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.item input {
        padding-left: 5px;
    }

    .invoice-box table tr.item td:first-child input {
        margin-left: -5px;
        width: 100%;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    .invoice-box input[type="number"] {
        width: 60px;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial,
        sans-serif;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }

</style>
