resource "aws_instance" "tfer--i-0d1bedc5ea9109633_FitPlaceWebServer" {
  ami                         = "ami-0ca38c7440de1749a"
  associate_public_ip_address = "true"
  availability_zone           = "ap-northeast-1d"

  capacity_reservation_specification {
    capacity_reservation_preference = "open"
  }

  cpu_core_count       = "1"
  cpu_threads_per_core = "1"

  credit_specification {
    cpu_credits = "standard"
  }

  disable_api_termination = "false"
  ebs_optimized           = "false"

  enclave_options {
    enabled = "false"
  }

  get_password_data                    = "false"
  hibernation                          = "false"
  instance_initiated_shutdown_behavior = "stop"
  instance_type                        = "t2.micro"
  ipv6_address_count                   = "0"
  key_name                             = "my-key"

  metadata_options {
    http_endpoint               = "enabled"
    http_put_response_hop_limit = "1"
    http_tokens                 = "optional"
  }

  monitoring = "false"
  private_ip = "10.0.1.10"

  root_block_device {
    delete_on_termination = "true"
    encrypted             = "false"

    tags = {
      Name = "FitPlaceWebServer"
    }

    volume_size = "20"
    volume_type = "gp2"
  }

  source_dest_check = "true"
  subnet_id         = "${data.terraform_remote_state.subnet.outputs.aws_subnet_tfer--subnet-09811307a61296e85_id}"

  tags = {
    Name = "FitPlaceWebServer"
  }

  tags_all = {
    Name = "FitPlaceWebServer"
  }

  tenancy                = "default"
  vpc_security_group_ids = ["${data.terraform_remote_state.sg.outputs.aws_security_group_tfer--WEB-SG_sg-0c2453e6dd2032480_id}"]
}
