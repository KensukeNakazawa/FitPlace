resource "aws_main_route_table_association" "tfer--vpc-002db229cd6373519" {
  route_table_id = "${data.terraform_remote_state.route_table.outputs.aws_route_table_tfer--rtb-05772d7d7e237fca9_id}"
  vpc_id         = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-002db229cd6373519_id}"
}

resource "aws_main_route_table_association" "tfer--vpc-72c2cc15" {
  route_table_id = "${data.terraform_remote_state.route_table.outputs.aws_route_table_tfer--rtb-60e28506_id}"
  vpc_id         = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-72c2cc15_id}"
}
