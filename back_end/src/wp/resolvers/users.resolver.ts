import { Args, Mutation, Query, Resolver } from '@nestjs/graphql';
import { PrismaService } from 'src/prisma.service';
import { wp_users } from '../models/users.model';

@Resolver(() => wp_users)
export class UsersResolver {
  constructor(private prisma: PrismaService) {}

  @Query(() => [wp_users])
  async users() {
    return this.prisma.wp_users.findMany();
  }

//   @Mutation(() => wp_users)
//   async createPost(
//     @Args('title') title: string,
//     @Args('content') content: string,
//   ) {
//     return this.prisma.wp_users.create({ data: { title, content } });
//   }
}