class Charter {
    constructor() {
        this.labels = [
            [ '2015', '2016', '2017', '2018' ],
            [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ]
        ];
        this.data = [];
        this.config = [];
        this.chart;
    }
    setupCharts(reg_name, sd_nat_YOY, sd_reg_YOY, sd_nat_MOM, sd_reg_MOM ){
        //NOTE: 0 - RvN YoY | 1 - R(all) MoM | 2 - R(2015) MoM | 3 - R(2016) MoM | 4 - R(2017) MoM | 5 - R(2018) MoM
        //Push Data For Chart 0
        this.data.push({
            labels: this.labels[0],
            datasets: [{
                label: 'National',
                backgroundColor: '#4a7337',
                borderColor: '#4a7337',
                data: sd_nat_YOY,
            }, {
                label: reg_name,
                backgroundColor: '#6b8c21',
                borderColor: '#6b8c21',
                data: sd_reg_YOY,
            }]
        });
        //Push Data For Chart 1
        this.data.push({
            labels: this.labels[1],
            datasets: [{
                label: 2015,
                backgroundColor: '#4a7337',
                borderColor: '#4a7337',
                data: sd_reg_MOM[0],
            }, {
                label: 2016,
                backgroundColor: '#6b8c21',
                borderColor: '#6b8c21',
                data: sd_reg_MOM[1],
            }, {
                label: 2017,
                backgroundColor: '#cda989',
                borderColor: '#cda989',
                data: sd_reg_MOM[2],
            }, {
                label: 2018,
                backgroundColor: '#704012',
                borderColor: '#704012',
                data: sd_reg_MOM[3],
            }]
        });
        //Push Data For Charts 2-5
        for ( var i = 0; i < 4; i++ ) {
            this.data.push({
                labels: this.labels[1],
                datasets: [{
                    label: 'National',
                    backgroundColor: '#4a7337',
                    borderColor: '#4a7337',
                    data: sd_nat_MOM[i],
                }, {
                    label: reg_name,
                    backgroundColor: '#6b8c21',
                    borderColor: '#6b8c21',
                    data: sd_reg_MOM[i],
                }]
            });
        }
        //Generate Config Object Array
        for ( var i = 0; i < 6; i++ ) {
            this.config.push( { type: 'line', data: this.data[i], options: {} } );
        }
    }
    updateChartView( ctrl ) {
        if ( ctrl <= 5 ) { 
            if ( typeof this.chart !== 'undefined' ) { this.chart.destroy(); }
            this.chart = new Chart( document.getElementById('chartviewport'), this.config[ctrl] );
            console.log("Chart Updated (" + ctrl + ")");
        }
    }
}
