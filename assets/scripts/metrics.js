class Charter {
    constructor( ir0, ir1, ir2, ir3, ir4, ic0, ic1, ic2, ic3, ic4, iu0, iu1, iu2, iu3, im0, im1, im2) {
        this.labels_i = [ ['2015','2016','2017','2018'],
                        ['PLU4046', 'PLU4225','PLU4770'] ];
        this.labels_o_r = ir0;
        this.labels_o_c = ic0;
        
        this.data_r_4046 = ir1;
        this.data_r_4225 = ir2;
        this.data_r_4770 = ir3;
        this.data_r_unc  = ir4;
        
        this.data_c_4046 = ic1;
        this.data_c_4225 = ic2;
        this.data_c_4770 = ic3;
        this.data_c_unc  = ic4;
        
        this.data_u_4046 = iu0;
        this.data_u_4225 = iu1;
        this.data_u_4770 = iu2;
        this.data_u_unc  = iu3;
        
        this.metrics_labels = im0;
        this.metrics_data = im1;
        this.demographics = im2;
        
        this.chart = [];
    }
    makeDemographicsChart() {
        this.chart[0] = new Chart( document.getElementById("chart0"), {
            type: 'pie',
            data: {
                labels: ['1','2','3','4','5'],
                datasets: [{
                    backgroundColor: ['#4a7337', '#6b8c21', '#cda989', '#ddd48f', '#704012'],
                    data: this.demographics
                }]
            },
            options: {}
        });
    }
    makeMetricsChart() {
        this.chart[5] = new Chart( document.getElementById("chart5"), {
            type: 'bar',
            data: {
                labels: this.metrics_labels,
                datasets: [{
                    label: 'Page Requests',
                    backgroundColor: ['#4a7337', '#6b8c21', '#cda989', '#ddd48f', '#704012'],
                    data: this.metrics_data
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: false
                }
            }
            
        });
        
    }
    makeUSChart() {
        var config_us_data = {
            labels: this.labels_i[0],
            datasets: [
                {
                    label: "PLU#4046",
                    backgroundColor: "#4a7337",
                    borderColor: "#4a7337",
                    borderWidth: 1,
                    data: this.data_u_4046
                },{
                    label: "PLU#4225",
                    backgroundColor: "#6b8c21",
                    borderColor: "#6b8c21",
                    borderWidth: 1,
                    data: this.data_u_4225
                },{
                    label: "PLU#4770",
                    backgroundColor: "#ddd48f",
                    borderColor: "#ddd48f",
                    borderWidth: 1,
                    data: this.data_u_4770
                },{
                    label: "Uncategorized",
                    backgroundColor: "#cda989",
                    borderColor: "#cda989",
                    borderWidth: 1,
                    data: this.data_u_unc
                }
            ]
        };
        if ( typeof this.chart[1] !== 'undefined' ) { this.chart[1].destroy(); }
        this.chart[1] = new Chart( document.getElementById('chart1'), { type: 'bar', data: config_us_data, options: {} });
    }
    makeTopCharts(input) {
        var ctrl = 0;
        switch( input ) {
            case "2015": ctrl = 0; break;
            case "2016": ctrl = 1; break;
            case "2017": ctrl = 2; break;
            case "2018": ctrl = 3; break;
        }
        //PREPARE CHART DATA
        var config_regions_data = {
            labels: this.labels_o_r[ctrl],
            datasets: [
                {
                    label: "PLU#4046",
                    backgroundColor: "#4a7337",
                    borderColor: "#4a7337",
                    borderWidth: 1,
                    data: this.data_r_4046[ctrl]
                },{
                    label: "PLU#4225",
                    backgroundColor: "#6b8c21",
                    borderColor: "#6b8c21",
                    borderWidth: 1,
                    data: this.data_r_4225[ctrl]
                },{
                    label: "PLU#4770",
                    backgroundColor: "#ddd48f",
                    borderColor: "#ddd48f",
                    borderWidth: 1,
                    data: this.data_r_4770[ctrl]
                },{
                    label: "Uncategorized",
                    backgroundColor: "#cda989",
                    borderColor: "#cda989",
                    borderWidth: 1,
                    data: this.data_r_unc[ctrl]
                }
            ]
        };
        var config_cities_data = {
            labels: this.labels_o_c[ctrl],
            datasets: [
                {
                    label: "PLU#4046",
                    backgroundColor: "#4a7337",
                    borderColor: "#4a7337",
                    borderWidth: 1,
                    data: this.data_c_4046[ctrl]
                },{
                    label: "PLU#4225",
                    backgroundColor: "#6b8c21",
                    borderColor: "#6b8c21",
                    borderWidth: 1,
                    data: this.data_c_4225[ctrl]
                },{
                    label: "PLU#4770",
                    backgroundColor: "#ddd48f",
                    borderColor: "#ddd48f",
                    borderWidth: 1,
                    data: this.data_c_4770[ctrl]
                },{
                    label: "Uncategorized",
                    backgroundColor: "#cda989",
                    borderColor: "#cda989",
                    borderWidth: 1,
                    data: this.data_c_unc[ctrl]
                }
            ]
        };
        
        //CREATE CHARTS
        if ( typeof this.chart[2] !== 'undefined' ) { this.chart[2].destroy(); }
        this.chart[2] = new Chart( document.getElementById('chart2'), { type: 'bar', data: config_regions_data, options: {} });
        if ( typeof this.chart[3] !== 'undefined' ) { this.chart[3].destroy(); }
        this.chart[3] = new Chart( document.getElementById('chart3'), { type: 'bar', data: config_cities_data, options: {} });

    }
}
