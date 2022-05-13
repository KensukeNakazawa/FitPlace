resource "aws_route_table_association" "tfer--subnet-09811307a61296e85" {
  route_table_id = "${data.terraform_remote_state.route_table.outputs.aws_route_table_tfer--rtb-0108ffab34717cdc4_id}"
  subnet_id      = "${data.terraform_remote_state.subnet.outputs.aws_subnet_tfer--subnet-09811307a61296e85_id}"
}
